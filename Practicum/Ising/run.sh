#!/bin/bash
for ((i=33 ; i < 80 ; i+=2))
do
  for len in 50 100 150
  do
  sbatch --wrap="srun ../a.out $len $i"
  done
done
