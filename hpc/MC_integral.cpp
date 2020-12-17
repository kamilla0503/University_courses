//
// Created by kamilla on 10/17/20.
//

#include <iostream>
#include <chrono>
#include <thread>
#include <vector>
#include<mutex>
#include<random>
#include <future>
#include <atomic>

const double a = -2.0;
const double b = 2.0;
const double S0 = 16.0;

double S = 0.0;
int steps = 10000000;

std::mutex m;

std::atomic_flag lock = ATOMIC_FLAG_INIT;

bool condition_1(const double&x, const double&y)
{
    return (-2*x*x+y*y*y)<-1;
}

bool condition_2(const double& x, const double& y)
{
    return (x*x*x+2*y)<3;
}

void integrate_one_thread(const int& n_steps, const double& a, const double&b)
{
    std::default_random_engine generator;
    std::uniform_real_distribution<double> distribution(a,b);

    double x, y;
    int cum_s = 0;

    for (int i =0; i<n_steps; i++ )
    {
        x = distribution(generator);
        y = distribution(generator);
        if (condition_1(x,y) && condition_2(x, y))
            cum_s = cum_s +1;
    }
    m.lock();
    S = S + (double)cum_s/n_steps;
    m.unlock();
    return ;
}

void thread_mutex_part()
{
    int c = std::thread::hardware_concurrency();

    S = 0.0;

    std::chrono::steady_clock::time_point begin = std::chrono::steady_clock::now();

    std::vector<std::thread> v;

    int intervals = steps/c;
    for(int i = 0; i < c; i ++)
    {

        std::thread th(integrate_one_thread, intervals, a, b);
        v.push_back(std::move(th));

    }
    for(auto& t : v)
        t.join();


    S = S0*S/(double)c;

    std::chrono::steady_clock::time_point end = std::chrono::steady_clock::now();
    std::chrono::duration<double> elapsed_seconds = end-begin;

    std::cout <<"Test   "  << std::to_string(c) << " threads"<< std::endl;
    std::cout << "Execution time = " << elapsed_seconds.count() << "s" << std::endl;
    std::cout << "Result: " << S << "\n";

}


int integrate_async (const int& n_steps, const double& a, const double&b)
{
    std::default_random_engine generator;
    std::uniform_real_distribution<double> distribution(a,b);

    double x, y;
    int cum_s = 0;

    for (int i =0; i<n_steps; i++ )
    {
        x = distribution(generator);
        y = distribution(generator);
        if (condition_1(x,y) && condition_2(x, y))
            cum_s = cum_s +1;
    }

    return cum_s;
}


void async_part()
{

    int c = std::thread::hardware_concurrency();
    int intervals = steps/c;


    std::chrono::steady_clock::time_point begin = std::chrono::steady_clock::now();

    std::vector <std::future<int>> futures;

    for (int i=0; i< c; i++)
    {
        futures.push_back(async(std::launch::async, integrate_async, intervals,  a, b));
    }

    int count = 0;
    for (int i = 0; i< futures.size(); i++ )
    {
        count += futures[i].get();
    }

    double S_result = (double)count/steps*S0;

    std::chrono::steady_clock::time_point end = std::chrono::steady_clock::now();
    std::chrono::duration<double> elapsed_seconds = end-begin;
    std::cout << "Async Result: " <<  S_result << "\n";
    std::cout << "Async Execution time = " << elapsed_seconds.count() << "s" << std::endl;
}

void integrate_with_atomic(const int& n_steps, const double& a, const double&b)
{
    std::default_random_engine generator;
    std::uniform_real_distribution<double> distribution(a,b);

    double x, y;
    int cum_s = 0;

    for (int i =0; i<n_steps; i++ )
    {
        x = distribution(generator);
        y = distribution(generator);
        if (condition_1(x,y) && condition_2(x, y))
            cum_s = cum_s +1;
    }

    while (lock.test_and_set(std::memory_order_acquire));
        S = S + (double)cum_s/n_steps;
    lock.clear(std::memory_order_release);
    return ;
}

void atomic_part()
{
    S=0.0;
    int c = std::thread::hardware_concurrency();

    S = 0.0;

    std::chrono::steady_clock::time_point begin = std::chrono::steady_clock::now();

    std::vector<std::thread> v;

    int intervals = steps/c;
    for(int i = 0; i < c; i ++)
    {

        std::thread th(integrate_with_atomic, intervals, a, b);
        v.push_back(std::move(th));

    }
    for(auto& t : v)
        t.join();


    S = S0*S/(double)c;

    std::chrono::steady_clock::time_point end = std::chrono::steady_clock::now();
    std::chrono::duration<double> elapsed_seconds = end-begin;

    std::cout <<"Test atomic  "  << std::to_string(c) << " threads"<< std::endl;
    std::cout << "Execution time = " << elapsed_seconds.count() << "s" << std::endl;
    std::cout << "Result: " << S << "\n";
}


int main()
{
    thread_mutex_part();

    async_part();

    atomic_part();

    return 0;
}