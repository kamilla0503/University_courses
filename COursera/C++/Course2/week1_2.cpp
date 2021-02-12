//
// Created by kamilla on 11/13/20.
//

#include<iostream>
#include<map>

#include<stdexcept>


using namespace std;

template<typename Key, typename Value>
Value&     GetRefStrict (map<Key, Value>&m, Key k);

template<typename Key, typename Value>
Value&     GetRefStrict (map<Key, Value>&m, Key k)
{
    if (m.count(k)>0)
    {
        return m[k];
    }
    throw runtime_error("");
}


int main()
{
    map<int, string> m = {{0, "value"}};
    string& item = GetRefStrict(m, 0);
    item = "newvalue";
    cout << m[0] << endl;

    return 0;
}