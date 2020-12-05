//
// Created by kamilla on 12/5/20.
//
# include <cstdlib>
#include <mpi.h>
#include<iostream>
# include <iomanip>
#include <vector>
#include<omp.h>
# include <cmath>
#include<fstream>
using namespace std;


int main ( int argc, char *argv[] )
{
    double a = 0.0;
    double b = 1.0;
    int id;
    int n;
    int p;

    MPI::Init ( argc, argv );

    MPI_Comm_rank(MPI_COMM_WORLD, &id);

    MPI_Comm_size(MPI_COMM_WORLD, &p);

    n = atoi(argv[1]);
    int N = n;
    int h = N/p;

    n = h;

    vector<double> input (N, 0);

    if (id == 0) {  //Задаем начальное условие на нулевом процессе
        for (int i = 0; i < N; ++i) {
            input[i] =  1.0;
        }
    }

/*    if (N%p==0)
    {
        h=N/p;
    }
    else
    {
        if (id!=0)
        {
            h=N/p;
        }
        else
        {
            h=N/p + N%p;
        }
    }*/


    vector <double> subset_input(h, 0);

    MPI_Scatter(input.data(), h, MPI_DOUBLE, &subset_input.front(),
                h, MPI_DOUBLE, 0, MPI_COMM_WORLD);

    //cout<< subset_input.size() << " points in interval "<< id << endl;

    double left=1, right=1;


    MPI::Status status;
    double t;
    double t_del;
    double t_max;
    int tag;
    double wtime;
    double *x;
    double x_del;

    vector <double> subset_new (h, 0);

    double k=1.0; //КОэффициент в уравнении

    x_del = ( b - a ) / ( double ) ( N - 1 );
    t_del =  x_del*x_del/2.0; //0.0002;

    int j_max= 10000000;//0.0001/t_del;


    wtime = MPI_Wtime ( );

    for (int  j = 1; j <= j_max; j++ )
    {
//
//  Determine new time.
//
/*        t_new = ( ( double ) (         j - j_min ) * t_max
                  + ( double ) ( j_max - j         ) * t_min )
                / ( double ) ( j_max     - j_min );*/
//
//  To set H_NEW(1:N), update the temperature based on the four point stencil.
//
        //for (int i = 1; i < subset_input.size()-1; i++ )


        for (int i = 1; i < h ; i++ )
        {
            subset_new[i] = subset_input[i] + t_del * (
                    k * ( subset_input[i-1] - 2.0 * subset_input[i] + subset_input[i+1] ) / x_del / x_del
            );
        }

        if ( id ==0 )
        {
            subset_new[n-1]= subset_input[n-1] + t_del * (
                    k * ( subset_input[n-2] - 2.0 * subset_input[n-1] + right ) / x_del / x_del
            );
        }
        else if (id == p-1)
        {
            subset_new[0] = subset_input[0] + t_del * (
                    k * ( left - 2.0 * subset_input[0] + subset_input[1] ) / x_del / x_del
            );
        }
        else
        {
            subset_new[n-1]= subset_input[n-1] + t_del * (
                    k * ( subset_input[n-2] - 2.0 * subset_input[n-1] + right ) / x_del / x_del
            );

            subset_new[0] = subset_input[0] + t_del * (
                    k * ( left - 2.0 * subset_input[0] + subset_input[1] ) / x_del / x_del
            );
        }

        tag = 1;
        if ( id < p - 1 )
        {
            MPI::COMM_WORLD.Send ( &subset_new[n-1], 1, MPI::DOUBLE, id+1, tag );
        }
        if ( 0 < id )
        {
            MPI::COMM_WORLD.Recv ( &left, 1, MPI::DOUBLE, id-1, tag, status );
        }
        else
        {
            subset_new[0] = 0 ; //краевое условие
        }
        tag = 2;
        if ( 0 < id )
        {
            MPI::COMM_WORLD.Send ( &subset_new[0], 1, MPI::DOUBLE, id-1, tag );
        }
        if ( id < p - 1 )
        {
            MPI::COMM_WORLD.Recv ( &right, 1, MPI::DOUBLE, id+1, tag, status );
        }
        else
        {
            subset_new[n-1] = 0.0; //краевое условие
        }

        for (int  i = 0; i < subset_input.size() ; i++ )
        {
            subset_input[i] = subset_new[i];
        }

    }

    MPI_Gather(subset_input.data(), h, MPI_DOUBLE, input.data(), h, MPI_DOUBLE, 0,
               MPI_COMM_WORLD);


    wtime = MPI_Wtime ( ) - wtime;

    if ( id == 0 )
    {
        cout << "\n";
        cout << "  Wall clock elapsed seconds = " << wtime << "\n";


    }


    if (id==0)
    {

        string filename = "HEAT_EQUATION_"+to_string(p)+"_"+to_string(N)+".txt";
        ofstream myfile;
        myfile.open (filename);
        double x = 0;

        myfile << x_del << " " << t_del << " " << j_max<< " " << wtime << endl;
        //double sum = 0.0;
        for (int i = 0; i < N ; i++ )
        {
            myfile << x << " " << input[i] << endl;
            x=x+x_del;
            //sum = sum+input[i];
            //cout << setw(14) << input[i];
        }

        myfile.close();
        //cout << " \n sum " << sum << " \n";
    }

    MPI_Barrier(MPI_COMM_WORLD);

    MPI::Finalize ( );

    return 0;
}


