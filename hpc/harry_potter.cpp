#include <algorithm>
#include <chrono>
#include <fstream>
#include <iostream>
#include <string>
#include <thread>
#include <vector>
#include <future>
//#include <mutex>
using namespace std;


void old_main(); //main by Artem Maminov

int count_word_in_line_block(std::vector<std::string>& lines, string needle, int i_indexes, int i_threads )
{
    int c_count = 0;

    for (int i=i_indexes; i<lines.size(); i = i+i_threads )
    {
        std::transform(lines[i].begin(), lines[i].end(), lines[i].begin(), ::tolower);
        if (lines[i].find(needle) != std::string::npos) {
            c_count++;
        }
    }

    return c_count;
}


int main()
{
    int c = std::thread::hardware_concurrency();

    std::fstream newfile;
    std::ifstream fs("harry.txt");
    std::vector<std::string> lines;


    std::chrono::steady_clock::time_point begin = std::chrono::steady_clock::now();

    for (std::string line; std::getline(fs, line);) {
        lines.push_back(line);
    }

    std::cout<<lines.size()<<"\n";
    string needle = "harry";
    int count = 0;

    vector <future<int>> futures;

    for (int i=0; i< c; i++)
    {
        futures.push_back(async(std::launch::async, count_word_in_line_block, std::ref(lines), needle,  i, c ));
    }
    
    for (int i = 0; i< futures.size(); i++ )
    {
        count += futures[i].get();
    }

    std::chrono::steady_clock::time_point end = std::chrono::steady_clock::now();
    std::chrono::duration<double> elapsed_seconds = end-begin;
    std::cout << "Found " << count << " matches" << std::endl;
    std::cout << "Async Execution time = " << elapsed_seconds.count() << "s" << std::endl;

    old_main();


    return 0;
}



void old_main(){
    std::fstream newfile;
    std::ifstream fs("harry.txt");
    std::vector<std::string> lines;

    std::chrono::steady_clock::time_point begin = std::chrono::steady_clock::now();

    for (std::string line; std::getline(fs, line);) {
        lines.push_back(line);
    }

    std::cout<<lines.size()<<"\n";
    string needle = "harry";
    int count = 0;
    for (std::string str : lines) {
        std::transform(str.begin(), str.end(), str.begin(), ::tolower);
        if (str.find(needle) != std::string::npos) {
            count++;
        }
    }


    std::chrono::steady_clock::time_point end = std::chrono::steady_clock::now();
    std::chrono::duration<double> elapsed_seconds = end-begin;
    std::cout << "Found " << count << " matches" << std::endl;
    std::cout << "Not async Execution time = " << elapsed_seconds.count() << "s" << std::endl;

}
