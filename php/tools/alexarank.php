<?php

if (!class_exists('Alexa_Rank')):

    class Alexa_Rank {

        /**
         * Get the rank from alexa for the given domain
         * 
         * @param $domain
         * The domain to search on
         */
        public function get_rank($domain) {

            $url = "http://data.alexa.com/data?cli=10&dat=snbamz&url=" . $domain;

            try {
                //Initialize the Curl  
                $ch = curl_init();

                //Set curl to return the data instead of printing it to the browser.  
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);

                //Set the URL  
                curl_setopt($ch, CURLOPT_URL, $url);

                //Execute the fetch  
                $data = curl_exec($ch);

                //Close the connection  
                curl_close($ch);

                $xml = new SimpleXMLElement($data);
                //Get popularity node
                $popularity = $xml->xpath("//POPULARITY");

                //Get the Rank attribute
                if (isset($popularity[0]['TEXT'])) {
                    $rank = (string) $popularity[0]['TEXT'];
                } else {
                    $rank = 1000000000;
                }
            } catch (Exception $e) {
                echo "<pre>";
                print_r($e);
                echo "</pre>";

                $rank = 1000000000;
            }

            return $rank;
        }

    }

    

    

    

    

    

    
endif;