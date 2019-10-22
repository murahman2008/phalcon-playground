<?php
    use Phalcon\Mvc\Controller;
    
    class PokerController extends Controller
    {
        public function indexAction()
        {
            $randomCards = $this->_generateRandomCards();
            echo "<h4>Rrandom cards:</h4>";
            echo "<div style = 'background-color:#EEE;'>";
            echo "<pre>"; var_dump($randomCards); echo "</pre>";
            echo "</div>";

            echo "<h4>Is the hand Straight or Straight Flush:</h4>";
            echo "<div style = 'background-color:#EEE;'>";
            echo "<pre>"; 
            var_dump($this->_checkCardsForStraightOrStraightFlush($randomCards)); 
            //var_dump($this->_checkCardsForStraightOrStraightFlush(array('kd', 'jh', 'qc', '10s', 'ah'))); 
            echo "</pre>";
            echo "</div>";        
        }

        private function _generateRandomCards()
        {
            $cardNumberArray = $output = $usedIndex = array();
            for($i = 2; $i <= 10; $i++)
                $cardNumberArray[] = $i;

            $cardNumberArray = array_merge($cardNumberArray, array('k', 'q', 'j', 'a'));
            $cardTypeArray = array('d', 'h', 'c', 's');

            for($i = 0; $i < 5; $i++)
            {
                $indexFound = false;
                while($indexFound !== true)
                {
                    $cnIndex = rand(0, count($cardNumberArray) - 1);
                    $ctIndex = array_rand($cardTypeArray);
                    
                    if(!in_array($cnIndex.':'.$ctIndex, $usedIndex))
                    {
                        $usedIndex[] = $cnIndex.':'.$ctIndex;
                        $indexFound = true;
                    }
                }

                $output[] = $cardNumberArray[$cnIndex].$cardTypeArray[$ctIndex];
            }
            
            return $output;
        }

        private function _checkCardsForStraightOrStraightFlush(Array $input)
        {
            $sMap = array('k' => 13, 'q' => 12, 'j' => 11, 'a' => 1);
            $input = array_map(create_function('$a', 'return substr($a, 0, -1);'), $input);
            
            foreach($input as $key => $value)
                $input[$key] = isset($sMap[$value]) ? (int)$sMap[$value] : (int)$value;
            
            $input = array_unique($input);
            rsort($input);
            if(count($input) !== 5 || $input[0] - $input[3] !== 3)
                return false;
            
            if($input[4] === 1 && ($input[3] - $input[4] === 1 || $input[0] + $input[4] === 14))
                return true;
            else if($input[3] - $input[4] === 1)
                return true;

            return false;
        }
    }
?>