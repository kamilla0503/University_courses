//
// Created by kamilla on 11/13/20.
//

#include<vector>
#include<map>
#include<utility>

#include<iostream>

using namespace std;

template <typename First, typename Second>
pair<First, Second> Sqr (const pair<First, Second>& input);

template <typename Key, typename Value>
map <Key, Value> Sqr (const map<Key, Value>& input);

template <typename T>
vector<T> Sqr (const vector<T>& input);

template <typename T>
T Sqr(T x);

template <typename T>
T Sqr(T x)
{
    return x*x;
}

template <typename First, typename Second>
pair<First, Second> Sqr (const pair<First, Second>& input)
{
    First a = Sqr(input.first);
    Second b = Sqr(input.second);
    return make_pair(a, b);
}

template <typename Key, typename Value>
map <Key, Value> Sqr (const map<Key, Value>& input)
{
    map <Key, Value> result;
    for (const auto& i : input)
    {
        result[i.first] = Sqr(i.second);
    }
    return result;
}

template <typename T>
vector<T> Sqr (const vector<T>& input)
{
    vector<T> result;
    for (const auto& i : input)
    {
        result.push_back(Sqr(i));
    }

    return result;
}


/*
int main()
{

    vector<int> v = {1, 2, 3};
    cout << "vector:";
    for (int x : Sqr(v)) {
        cout << ' ' << x;
    }
    cout << endl;

    map<int, pair<int, int>> map_of_pairs = {
            {4, {2, 2}},
            {7, {4, 3}}
    };
    cout << "map of pairs:" << endl;
    for (const auto& x : Sqr(map_of_pairs)) {
        cout << x.first << ' ' << x.second.first << ' ' << x.second.second << endl;
    }


    return 0;
}
*/

