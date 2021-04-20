#include "mcmc.h"
#include <iostream>
#include <fstream>
#include <cassert>

//Lattice::Lattice() {};
std::random_device generator;
std::uniform_real_distribution<double> distribution(0.0,1.0);
//std::mt19937 generator(1234);
std::random_device generators1;
std::uniform_int_distribution<int> distribution1(0, 3);
//std::mt19937 generators1(123);
std::random_device generators2;
std::uniform_int_distribution<int> distribution2(0, 1);
//std::mt19937 generators3(12377);
std::random_device generators3;


Lattice::Lattice( long int max_seq_size ) {
    lattice_side = max_seq_size;
    //создается одномерный массив соседей на квадратной решетке
    long int x, y;
    div_t n;
    map_of_contacts_int.resize(lattice_side*lattice_side*ndim2());
    for (long int i =0; i<lattice_side*lattice_side; i++){
        map_of_contacts_int[4*i] = i+1;
        map_of_contacts_int[4*i+1] = i-1;
        map_of_contacts_int[4*i+2] = i+lattice_side;
        map_of_contacts_int[4*i+3] = i-lattice_side;
        n=div(i, lattice_side);
        x=n.rem;
        y=n.quot;
        for (int j =0; j<ndim2(); j++){
            if(x==0){
                map_of_contacts_int[4*i+1] = i+lattice_side-1;
            }
            if(x==(lattice_side-1)){
                map_of_contacts_int[4*i] = i-(lattice_side-1);
            }
            if(y==0){
                map_of_contacts_int[4*i+3] = lattice_side*(lattice_side-1)+x;
            }
            if(y==(lattice_side-1)){
                map_of_contacts_int[4*i+2] = x;
            }
        }
    }
}

void Lattice::create_lattice(long int max_seq_size ){

    lattice_side = max_seq_size;
    long int x, y;
    div_t n;
    map_of_contacts_int.resize(lattice_side*lattice_side*ndim2());
    x_coords.resize(lattice_side*lattice_side*ndim2());
    y_coords.resize(lattice_side*lattice_side*ndim2());
    for (long int i =0; i<lattice_side*lattice_side; i++){
        map_of_contacts_int[4*i] = i+1;
        map_of_contacts_int[4*i+1] = i-1;
        map_of_contacts_int[4*i+2] = i+lattice_side;
        map_of_contacts_int[4*i+3] = i-lattice_side;
        n=div(i, lattice_side);
        x=n.rem;
        y=n.quot;
        for (int j =0; j<ndim2(); j++){
            if(x==0){
                map_of_contacts_int[4*i+1] = i+lattice_side-1;
            }
            if(x==(lattice_side-1)){
                map_of_contacts_int[4*i] = i-(lattice_side-1);
            }
            if(y==0){
                map_of_contacts_int[4*i+3] = lattice_side*(lattice_side-1)+x;
            }
            if(y==(lattice_side-1)){
                map_of_contacts_int[4*i+2] = x;
            }
        }
        x_coords[i] = x;
        y_coords[i] = y;



    }

}

Protein::Protein() {}

Protein::Protein(long int n) {

    /** type | previous | next   **/
    lattice.create_lattice(n); //создание решетки, на 5 больше, чем длина цепочки
    // 0 - отстуттвие элемента на решетке
    sequence_on_lattice.resize(lattice.lattice_side*lattice.lattice_side, 1); //последовательность мономеров
    number_of_monomers = n*n;

    for (int i =0; i<number_of_monomers; i++){
        sequence_on_lattice[i]  = distribution2(generators2);
    }
    count_contacts();


}

void Protein::count_contacts()
{
    long int hh = 0;
    long int current_position = start_conformation;
    coord_t  step;
    long int mag = 0;
    for (int i =0; i<number_of_monomers; i++){
        for ( int j=0; j<lattice.ndim2(); j++ ){
            step = lattice.map_of_contacts_int[lattice.ndim2()*i+j];
            if ( sequence_on_lattice[step]!=0  )
            {
                hh=hh+sequence_on_lattice[i] * sequence_on_lattice[step];
            }
        }
        mag = mag + sequence_on_lattice[i];
    }

    E = -(hh/2);
    current_H_counts = mag;
}




void Protein::MC( double J_in, double h_in, int Simulation, long int steps_to_equilibrium  , long int mc_steps, bool bradius  )
{
    nSimulation = Simulation;
    J = J_in;
    h = h_in;
    //[i,j]: i-поворот, j-направление (против часовой)
    int reflect_directions[4][4]=
            {{3, 2, 0, 1 }, //90
             {1,0,3,2 }, //180
             {2,3, 1, 0 }, //270
            { 0, 1, 2, 3}
            };

    int inverse_steps[4] = {1,0,3,2}; //для сохранения направлений в апдейте "перенести конец в начало"
    //double step_rd; //Для выбора апдейта: обычный или реконнект
    double q_rd, p1, p_metropolis; //Для вероятности принятия шага
    int rand_path; // = distribution1(generators1); //выбирается направление: 0 - переставляем начало в конец
    double typeOfUpdate; //0 - простой; 1 - реконнект
    long int step;
    int step_on_lattice;//выбор одного из соседей
    long int new_point;
    long int new_E, new_H;
    int hh;
    long int temp, del, oldspin;


    //std::uniform_int_distribution<long int> distribution_spin(0, number_of_monomers-1);
    std::uniform_int_distribution<long int> distribution_spin(0, lattice.lattice_side* lattice.lattice_side - 1 );
    //std::mt19937 generator_spin(123);
    std::random_device generator_spin;

    //вероятность добавить спин в кластер в кластерном апдейте
    P_add = 1 - exp(-2*J); //пока так для h=0

    double p_for_local_update = 0.6;
    double p_for_reconnect = 1.0; //p_for_local_update - p_for_reconnect = вероятность реконнекта

    //spins_in_cluster.resize(number_of_monomers, false);

    long int all_steps=steps_to_equilibrium+mc_steps;

    for (long int i=0; i<all_steps+2; i++) {
        //std::cout << "STEP : " << i << std::endl;


            long int coord = distribution_spin(generator_spin);
                int sign = sequence_on_lattice[coord];

                std::valarray<bool> used_coords;
                used_coords.resize(lattice.lattice_side*lattice.lattice_side, false  );

                std::queue<long int> Cluster;

                Cluster.push(coord);
                used_coords[coord] = true;

                while (!Cluster.empty()) {
                    temp = Cluster.front();
                    Cluster.pop();

                    for (int j = 0; j < lattice.ndim2(); j++)
                    {
                        step = lattice.map_of_contacts_int[lattice.ndim2() * temp + j];
                        double p = distribution(generator);
                        //???
                        if (sequence_on_lattice[step] == sign && p < P_add &&
                            !used_coords[step]) {
                            Cluster.push(step);
                            used_coords[step]= true;
                            sequence_on_lattice[step] *= -1;
                        }
                    }
                }
                sequence_on_lattice[coord] *= -1;

                count_contacts();

                std::cout <<  current_H_counts  << std::endl;




        if (i > steps_to_equilibrium) {
            save_calcs();


        }


        //if ( i> steps_to_equilibrium && i%1000000000==0 )
        if ( i> steps_to_equilibrium && i%1000==0 )
        {
            std::string filename;
            std::ofstream out_result;

            filename = "BC_"+std::to_string(J)+"_"+std::to_string(h)+"_"+std::to_string(number_of_monomers)+"_"+std::to_string(nSimulation)+".txt";
            //filename = "Radius_"+std::to_string(J)+"_"+std::to_string(number_of_monomers)+"_CanonicalIsing.txt";

            out_result.open(filename);
            //out_result << mc_steps<<" " << number_of_monomers << " " << J << " " << h  <<   " ";
            out_result << "N J h mean_R_sq err_mean_R_sq mean_R_gyr_sq err_mean_R_gyr_sq ";
            out_result << "mean_e err_mean_e mean_e_sq err_mean_e_sq mean_e_fourth err_mean_e_fourth ";
            out_result << "mean_m err_mean_m mean_m_sq err_mean_m_sq mean_m_fourth err_mean_m_fourth " << std::endl;

            out_result << number_of_monomers << " " << J << " " << h <<  " ";
            out_result << dists.mean() << " " << dists.errorbar()<< " " << gyration.mean() << " " << gyration.errorbar() << " ";

            out_result << energy.mean() << " " << energy.errorbar() << " ";
            out_result << energy_sq.mean() << " " << energy_sq.errorbar() << " ";
            out_result << energy_4.mean() << " " << energy_4.errorbar() << " ";

            out_result << magnetization.mean() << " " << magnetization.errorbar() << " ";
            out_result << magnetization_sq.mean() << " " << magnetization_sq.errorbar() << " ";
            out_result << magnetization_4.mean() << " " << magnetization_4.errorbar() << " ";
            out_result << i << std::endl; //neede i!!!!


            out_result.close();

            filename = "Counts_E_Ising_"+std::to_string(J)+"_"+std::to_string(number_of_monomers)+"_"+std::to_string(nSimulation)+".txt";


            out_result.open(filename);

            out_result << "N J h steps " <<  std::endl;
            out_result << number_of_monomers << " " << J << " " << h <<  " ";
            out_result << i << std::endl;
            for ( auto counts : count_E )
            {
                out_result << counts.first << " " << counts.second << std::endl;
            }
            out_result.close();


            filename = "Counts_M_Ising_"+std::to_string(J)+"_"+std::to_string(number_of_monomers)+"_"+std::to_string(nSimulation)+".txt";

            out_result.open(filename);

            out_result << "N J h steps " <<  std::endl;
            out_result << number_of_monomers << " " << J << " " << h <<  " ";
            out_result << i << std::endl;
            for ( auto counts : count_M )
            {
                out_result << counts.first << " " << counts.second << std::endl;
            }
            out_result.close();


        }


    }

}



void Protein::save_calcs()
{

    energy << 1.0*(E)/number_of_monomers;
    energy_sq << 1.0*(E)/number_of_monomers* 1.0*(E)/number_of_monomers;
    energy_4 << 1.0*(E)/number_of_monomers* 1.0*(E)/number_of_monomers* 1.0*(E)/number_of_monomers* 1.0*(E)/number_of_monomers;

    magnetization << 1.0*abs(current_H_counts)/number_of_monomers;
    magnetization_sq << 1.0*current_H_counts/number_of_monomers* 1.0*current_H_counts/number_of_monomers;
    magnetization_4 << 1.0*current_H_counts/number_of_monomers* 1.0*current_H_counts/number_of_monomers* 1.0*current_H_counts/number_of_monomers* 1.0*current_H_counts/number_of_monomers;


    count_E[E] = count_E[E] + 1;
    count_M[current_H_counts] = count_M[current_H_counts] + 1;

    //radius();


}