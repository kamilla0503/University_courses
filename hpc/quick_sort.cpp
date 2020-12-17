//
//  
//

#include <omp.h>
#include <iostream>
#include<vector>
#include<algorithm>
#include<fstream>

using namespace std;

const int N = 1e6;
const int N_short = 1000; //Выбрала просто тыканьем
const int M = 1e7;



int partition(vector<int> &values, int left, int right) {
    int pivotIndex = left + (right - left) / 2; //опорный элемент - почти медиана
    int pivotValue = values[pivotIndex];
    int i = left, j = right;
    int temp;
    while(i <= j) {
        while(values[i] < pivotValue) {
            i++;
        }
        while(values[j] > pivotValue) {
            j--;
        }
        if(i <= j) {
            temp = values[i];
            values[i] = values[j];
            values[j] = temp;
            i++;
            j--;
        }
    }
    return i;
}

void quicksort(vector<int> &values, int left, int right) {
    if(left < right) {
        int pivotIndex = partition(values, left, right);

if( (right-left) < N_short) //однопоточное выполнение коротких отрезков
{
    quicksort(values, left, pivotIndex - 1);
    quicksort(values, pivotIndex, right);
}
        else{
#pragma omp task shared(values)
        quicksort(values, left, pivotIndex - 1);
#pragma omp task shared(values)
        quicksort(values, pivotIndex, right);
#pragma omp taskwait
    }
    }
}


int old_main() {

    int n_max = omp_get_num_procs();


    for (int n_threads = 1; n_threads<=n_max; n_threads++) {

        vector<int> input(N);
        srand(123);
#pragma omp parallel for ordered
        for(int i=0;i<N;i++) {
            input[i] = rand() % M;
        }

        double start = omp_get_wtime();
        omp_set_num_threads(n_threads);

        double start_t1 = omp_get_wtime();

#pragma omp parallel
#pragma omp single
        quicksort( input, 0, N - 1);

        double t1 = omp_get_wtime() - start_t1;

        cout << "n_threads: " << n_threads <<  endl;
        cout << "Time: " << t1 <<  endl;

        vector <int> compare = input;
        sort (compare.begin(), compare.end());

        cout << "Check via algorithm lib  " << (input == compare) << endl;


    }



    return 0;
}


int main ()
{
    int n_runs = 100;

    int n_max = omp_get_num_procs();

    ofstream myfile;
    myfile.open ("qsort_time_omp.txt");

    for (int n_threads = 1; n_threads<=n_max; n_threads++)
    {
        double sum = 0.0;
        double sum_sq = 0.0;
        double time;
        for (int i = 0; i<n_runs; i++)
        {
            vector<int> input(N);
            //srand(123);
#pragma omp parallel for ordered
            for(int i=0;i<N;i++) {
                input[i] = rand() % M;
            }

            double start = omp_get_wtime();
            omp_set_num_threads(n_threads);

            double start_t1 = omp_get_wtime();

#pragma omp parallel
#pragma omp single
            quicksort( input, 0, N - 1);

            double time = omp_get_wtime() - start_t1;
            sum = sum +  time;
            sum_sq = sum_sq + time*time;



        }
        double mean = sum/double(n_runs);
        double var =  sum_sq - mean*mean;

        myfile<< n_threads << " " << mean  << " " << var << endl;


    }

    myfile.close();

    return 0;
}