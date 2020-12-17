//
// Created by kamilla on 10/31/20.
//

#include <iostream>
#include <chrono>
#include<omp.h>
#include <vector>
#include<numeric>
#include <cmath>
using namespace std;


const int  N = 10000;
const double a = 0.0;
const double b = 10.0;

double f(double x1, double x2 )
{
    return (x1-1)*(x1-1)+(x2-2.5)*(x2-2.5);
}

int main ()
{
    int n_max = omp_get_num_procs();

    double h = (b-a)/(double)N;

    for (int n_threads = 1; n_threads<=n_max; n_threads++)
    {
        //omp_lock_t lock;  //оказался дороже по времени
        //omp_init_lock(&lock);

        double dMin = -numeric_limits<double>::lowest ();
        int di=-1; int dj=-1;

        double start = omp_get_wtime();
        omp_set_num_threads(n_threads);

//#pragma omp parallel for default(shared) collapse(2)
//#pragma omp parallel for collapse(2)  //Параллелим квадрат, так что можно было бы использовать,
//но я проверяла на свом случае  - дешевле один раз получить x1
#pragma omp parallel for
        for (int i = 0; i < N; i++) {
            double x1 = a+i*h;
            for (int j = 0; j < N; j++){
                //double x1 = a+i*h;
                double x2 = a+j*h;

                double currentResult = f(x1, x2);

                if (currentResult<dMin)
                {
#pragma omp critical
                    //omp_set_lock(&lock);
                    dMin = currentResult;
                    di = i;
                    dj = j;
                    //omp_unset_lock(&lock);
                }
            }

        }

        std::cout << "Nthreads = " << n_threads << " Times  = " << omp_get_wtime() - start  << std::endl;
        std::cout << "x1 = " <<  a+di*h<< "; x2 = " << a+dj*h<< std::endl;
        std::cout << "Min = " << dMin << std::endl;


    }



    return 0;
}