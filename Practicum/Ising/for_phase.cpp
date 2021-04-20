#include <iostream>
#include <fstream>
#include <omp.h>
#include "mcmc.h"
#include "observable.h"

int main(int argc, char *argv[]){

    std::vector<int> s_template = {1, 1};

    std::vector<double> epsilon_b = {0, 0.1, 0.2, 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1.0,1.1, 1.2,1.3,1.4,1.5,1.6};


    std::vector<double> fugacity_start = {-1.8020412910013206, -1.8063426875610387, -1.8112879617765285, -1.8167516078132924, -1.8230826002818394, -1.8302486106977311, -1.8384676042294354, -1.847997209214932, -1.8589949729231385, -1.871814014059603, -1.8868666028204277, -1.9044719684013118, -1.9252467449367747, -1.9496607449939674, -1.977277472336193, -2.0062226498554137, -2.082869476239464 };
    std::vector<double> fugacity_slow = {-1.664125963919561, -1.668488700099906, -1.6732781544126065, -1.6791518872426567, -1.6854840718897315, -1.693006202102418, -1.7016965156702237, -1.7117995154345376, -1.7237948542031603, -1.738267122629306, -1.755709551188707, -1.777577256717701, -1.8049061929853076, -1.8400337999750653, -1.8865282649823107, -1.9521615616662622, -2.011634483783167 };

    std::vector<double> result = {};
    int count_fails=0;
    int i = std::stoi(argv[1]);

    double h_f=0.005;

    h_f = 0.01;

    double current_f =  fugacity_start[i] - 0.3;
    std::string filename = "properties_isaw_moresteps_"+std::to_string(i)+"_higher_hpc.txt";

    std::ofstream out_result;
    out_result.open(filename);

    out_result << "epsilon fugacity mean_n err_n mean_e err_e mean_m err_m m2_n m2_e m2_m less5.6K " << std::endl;

    while(current_f<0.4+0.001){
        Protein p;
        result = p.MC( epsilon_b[i], current_f, 0, 1800000, 4000000000, false);

        out_result << epsilon_b[i] << " " << current_f << " ";
        for (int e = 0; e < result.size(); e++) {
            out_result << result[e] << " ";

        }

        out_result << std::endl;
        current_f = current_f + h_f;

    }

    out_result.close();


    return 0;
}
