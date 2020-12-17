//
// Created by kamilla on 9/26/20.
//

#include <iostream>
#include <chrono>
#include <thread>
#include <vector>
#include<mutex>

const double a = 0;
const double b = 1;

double S = 0;
double h = 0.0000001;


std::mutex m;


double f(double x) {
    return 4. / (1. + x * x);
}

void integrate(double start, double finish, int n_intervals)
{
    double cum_s = 0.0;
    double x1, x2;
    for (int i =0; i<n_intervals; i++ )
    {
        x1 = start + i*h;
        x2 = start + (i+1)*h;
        cum_s = cum_s + 0.5 * (f(x1)+f(x2)) *h;
    }
    m.lock();
    S = S + cum_s;
    m.unlock();
    return ;
}



int main()
{
    //int c = std::thread::hardware_concurrency();
    std::vector<int> tests = {1, 2, 4};

    for (int num_threads : tests)
    {
        S = 0.0;

        double start, finish;

        std::chrono::steady_clock::time_point begin = std::chrono::steady_clock::now();

        std::vector<std::thread> v;

        int intervals = b/h/num_threads;
        //std::cout << intervals << std:: endl;

        for(int i = 0; i < num_threads; i ++)
        {
            start = a+ h*intervals*i;
            finish = start+ h*intervals;
            //std:: cout << start << " " << finish << std::endl;
            std::thread th(integrate, start, finish, intervals);
            v.push_back(std::move(th));
        }
        for(auto& t : v)
            t.join();

        std::chrono::steady_clock::time_point end = std::chrono::steady_clock::now();
        std::chrono::duration<double> elapsed_seconds = end-begin;

        std::cout <<"Test   "  << std::to_string(num_threads) << " threads"<< std::endl;
        std::cout << "Execution time = " << elapsed_seconds.count() << "s" << std::endl;
        std::cout << "Result: " << S << "\n";
        std::cout << "Test finished " << std::endl;



    }







    return 0;
}