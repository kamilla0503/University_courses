//
// Created by kamilla on 11/21/20.
//

#include <mpi.h>
#include<iostream>
#include <vector>
#include<omp.h>

using namespace std;

vector<int> slicing(vector<int>& arr,
                    int X, int Y)
{

    // Starting and Ending iterators
    auto start = arr.begin() + X;
    auto end = arr.begin() + Y + 1;

    // To store the sliced vector
    vector<int> result(Y - X + 1);

    // Copy vector using copy function()
    copy(start, end, result.begin());

    // Return the final sliced vector
    return result;
}

template <typename T>
double compute_avg(std::vector <T> array) {
    float sum = 0;
    int i;
    for (i = 0; i < array.size(); i++) {
        sum += array[i];
    }
    return sum / array.size();
}


int main(int argc, char* argv[])

{
    srand(123);
    double sum;
    int N = 10000;
    MPI_Status st;
    MPI_Init(&argc,&argv);
    int r;
    MPI_Comm_rank(MPI_COMM_WORLD, &r);
    int p;
    MPI_Comm_size(MPI_COMM_WORLD, &p);

    int h = N/p;
 
    vector<int> input (N, 0);

    int elements_per_proc =h;


    if (r == 0) {
        for (int i = 0; i < N; ++i) {
            input[i] =  rand() % 1000;
        }
    }

    sum = 0.0;
    vector <int> subset_input (h, 0);

    MPI_Scatter(input.data(), h, MPI_INT, &subset_input.front(),
                h, MPI_INT, 0, MPI_COMM_WORLD);

    double sub_avg = compute_avg(subset_input );
    sum +=sub_avg;
    sum /= p;
/*    int x = world_rank*h;
    int y = world_rank*h+h-1;
    cout << x << " " << y << endl;
    vector <int> subset_input = slicing(input, x, y);
    double sub_avg = compute_avg(subset_input );*/
    if(r != 0) {
        MPI_Send(&sub_avg, 1, MPI_DOUBLE, 0, 0, MPI_COMM_WORLD);
    } else {
        double s;
        for(int i = 1; i < p; i ++) {
            MPI_Recv(&s, 1, MPI_DOUBLE, i, 0, MPI_COMM_WORLD, &st);
            sum += s/p;
        }

    }


    if ( r == 0){
        cout << "Mean value = " << sum << endl;
        cout << "Processes = " << p << endl;
        double original_data_avg =
                compute_avg(input );

        cout << " Without mpi, Mean value = " << original_data_avg << endl;
    }

    MPI_Barrier(MPI_COMM_WORLD);

    MPI_Finalize();


    return 0;
}

