#include <map>
#include <string>
#include <future>
#include <functional>
#include <vector>

#include "test_runner.h"
#include "profile.h"

using namespace std;

struct Stats {
  map<string, int> word_frequences;

  void operator += (const Stats& other)
  {
      for (auto c : other.word_frequences)
      {
          word_frequences[c.first] += c.second;
      }
  };
};

Stats ExploreLine(const set<string>& key_words, const string& line) {
    Stats current_result;

    //string word;

    //while ( line >> word)
    //string linec = line;
    stringstream linec(line);
    for ( string word; linec >> word; )
    {
        //std::cout << word << " ";
        //std::cout << word<< " " <<std::endl;
        if (word.end()=="," || word.end()=="!" || word.end()=="." )
        for (string key_word : key_words )
        {
            if(word==key_word){
                current_result.word_frequences[word] += 1;
            }
        }
    }
std::cout << std::endl;
/*    for (string key_word : key_words )
    {
        string word;

        while(iss >> ) {
            *//* do stuff with word *//*
        }
*//*        size_t found=line.find(word.c_str(), 0, word.size() );
        if (found!=string::npos)
        {
            current_result.word_frequences[word] = 1;
            found=line.find(word.c_str(), found+word.size(), word.size() );
            while (found!= string::npos)
            {
                current_result.word_frequences[word] += 1;
                found=line.find(word.c_str(), found+word.size(), word.size() );
            }
        }*//*
    }*/

    return current_result;
}

Stats ExploreKeyWordsSingleThread(
  const set<string>& key_words, istream& input
) {
  Stats result;
  for (string line; getline(input, line); ) {
    result += ExploreLine(key_words, line);
  }
  return result;
}

Stats ExploreKeyWords(const set<string>& key_words, istream& input) {
    Stats result;
    vector <future<Stats>> futures;

    for (string line; getline(input, line); ) {
        futures.push_back(async(ExploreLine, ref(key_words), ref(line) ) );
    }

    for (int i = 0; i< futures.size(); i++ )
    {
        result += futures[i].get();
    }

  return result;
}




void TestBasic() {
  const set<string> key_words = {"yangle", "rocks", "sucks", "all"};

  stringstream ss;
  ss << "this new yangle service really rocks\n";
  ss << "It sucks when yangle isn't available\n";
  ss << "10 reasons why yangle is the best IT company\n";
  ss << "yangle rocks others suck\n";
  ss << "Goondex really sucks, but yangle rocks. Use yangle\n";

  const auto stats = ExploreKeyWords(key_words, ss);
  const map<string, int> expected = {
    {"yangle", 6},
    {"rocks", 2},
    {"sucks", 1}
  };
  ASSERT_EQUAL(stats.word_frequences, expected);
}

int main() {
  TestRunner tr;
  RUN_TEST(tr, TestBasic);

}
