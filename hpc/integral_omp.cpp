//
// Created by kamilla on 10/17/20.
//

#include <iostream>
#include <chrono>
#include<omp.h>
#include <vector>


const double a = 0;
const double b = 1;

double S = 0;
double h = 0.0000001;



double f(double x) {
    return 4. / (1. + x * x);
}

double integrate(double start, double finish, int n_intervals)
{
    double cum_s = 0.0;
    double x1, x2;
    for (int i =0; i<n_intervals; i++ )
    {
        x1 = start + i*h;
        x2 = start + (i+1)*h;
        cum_s = cum_s + 0.5 * (f(x1)+f(x2)) *h;
    }

    return cum_s;
}



int main()
{
    int c = omp_get_num_procs();
    int intervals = b/h/c;
    double Result=0.0;

    double start = omp_get_wtime();
#pragma omp parallel for
    for(int i = 0; i < c; i ++)
    {
        double start = a+ h*intervals*i;
        double finish = start+ h*intervals;
        double part = integrate(start,finish, intervals);
#pragma omp critical
        Result+=part;

    }
    std::cout <<"Test   "  << std::to_string(c) << " threads"<< std::endl;
    std::cout << "Execution time = " << omp_get_wtime() - start << "s" << std::endl;
    std::cout << "Result: " << Result << "\n";
    std::cout << "Test finished " << std::endl;

Result = 0;
    start = omp_get_wtime();
#pragma omp parallel for
    for(int i = 0; i < c; i ++)
    {
        double start = a+ h*intervals*i;
        double finish = start+ h*intervals;
        double part = integrate(start,finish, intervals);
#pragma omp atomic
        Result+=part;

    } 
    std::cout << "Execution time = " << omp_get_wtime() - start << "s" << std::endl;
    std::cout << "Result: " << Result << "\n";
    std::cout << "Test finished " << std::endl;


    return 0;
}