<?php

class FileReader
{
    protected $file;
    protected $countDocs = 0;

    protected $startSearchStrings = 0;
    protected $countSearchStrings = 0;
    protected $wordMatrix = [];
    protected $searchMatrix = [];

    /**
     * @param string $filename
     * @throws Exception
     */
    public function __construct(string $filename = "input.txt")
    {
        if (!($this->file = file($filename))) throw new Exception("Cannot open file");
        $this->countDocs = (int)$this->file[0];
        if ($this->countDocs > 104) throw new Exception("Many docs given");
        $this->startSearchStrings = $this->countDocs + 2;
        $this->fillWordMatrix();
        $this->countSearchStrings = (int)$this->file[$this->countDocs + 1];
        if ($this->countSearchStrings > 104) throw new Exception("Many query given");
        $this->setSearchMatrix();
        $this->sort();
    }

    private function sort()
    {
        foreach ($this->searchMatrix as $key => &$val) {
            arsort($val);
        }
    }

    public function list()
    {
        foreach ($this->searchMatrix as $queryNum => $docNums) {
            $i = 0;
            echo "$queryNum: ";
            foreach ($docNums as $docNum => $weight) {
                $i++;
                if ($i > 5) break;
                echo "$docNum ";
            }
            echo PHP_EOL;
        }
    }

    private function fillWordMatrix()
    {
        for ($i = 1; $i <= $this->countDocs; $i++) {
            $line = $this->file[$i];
            $arr = explode(" ", $line);
            foreach ($arr as $word) {
                $word = preg_replace('~[\r\n]+~', '', $word);
                $this->wordMatrix[$word][$i] = isset($this->wordMatrix[$word][$i])
                    ? $this->wordMatrix[$word][$i] + 1
                    : 1;
            }
        }
    }

    private function setSearchMatrix()
    {
        for ($i = 0; $i < $this->countSearchStrings; $i++) {
            $line = $this->file[$i + $this->startSearchStrings];
            if (strlen($line) > 100) throw new Exception("Query to large");
            $arr = explode(" ", $line);
            $uniqueWords = [];
            foreach ($arr as $word) {
                $word = preg_replace('~[\r\n]+~', '', $word);
                if (!in_array($word, $uniqueWords) && array_key_exists($word, $this->wordMatrix)) {
                    foreach ($this->wordMatrix[$word] as $docNum => $weight) {
                        $this->searchMatrix[$i + 1][$docNum] = !isset($this->searchMatrix[$i + 1][$docNum])
                            ? $weight
                            : $this->searchMatrix[$i + 1][$docNum] + $weight;
                    }
                    $uniqueWords[] = $word;
                }
            }
        }
    }
}

$fr = new FileReader();
$fr->list();