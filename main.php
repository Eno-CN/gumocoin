<?php
// 区块链

/**
 * Class Block
 * @desc 区块
 */
class Block{
    public $data;
    public $hash;
    public $previousHash;

    public function __construct($data,$previousHash)
    {
        $this->data = $data;
        $this->previousHash = $previousHash;
        $this->hash = $this->computeHash();
    }

    public function computeHash(){
        return hash('sha256',$this->data.$this->previousHash);
    }
}

//$block = new Block('转账十元','123');
//var_dump($block);

/**
 * Class Chain
 * @desc 链
 */
class Chain{
    public $chain;

    public function __construct()
    {
        $this->chain = [$this->bigBang()];
    }

    //生成祖先区块
    private function bigBang(){
        return new Block('我是祖先','');
    }

    public function getLatestBlock(){
        return end($this->chain);
    }

    //添加区块到区块链
    public function addBlockToChain(Block &$newBlock){
        //data
        //找到最近一个block的hash，这个hash就是最新区块的previousHash
        $newBlock->previousHash = $this->getLatestBlock()->hash;
        $newBlock->hash = $newBlock->computeHash();
        array_push($this->chain,$newBlock);
    }

    //验证当前区块链是否合法
    //当前的数据是否被篡改
    //验证区块的previousHash是否等于previous区块的hash
    public function validateChain(){
        if (count($this->chain) === 1){
            if($this->chain[0]->hash !== $this->chain[0]->computeHash()){
                return false;
            }
            return true;
        }

        //从第二个区块开始验证到最后一个区块
        for ($i = 1;$i <= count($this->chain)-1;$i++){
            $blockToValidate = $this->chain[$i];
            if ($blockToValidate->hash !== $blockToValidate->computeHash()){
                echo '数据篡改',"\n";
                return false;
            }
            $previousBlock = $this->chain[$i-1];
            if ($blockToValidate->previousHash !== $previousBlock->hash){
                echo '前后区块链断裂',"\n";
                return false;
            }
        }

        return true;
    }
}

$gumoChain = new Chain();
$block1 = new Block('转账十元','');
$gumoChain->addBlockToChain($block1);
$block2 = new Block('转账十个十元','');
$gumoChain->addBlockToChain($block2);

//尝试篡改数据
//$gumoChain->chain[1]->data = '转账一百个十元';

//尝试篡改数据和hash
$gumoChain->chain[1]->data = '转账一百个十元';
$gumoChain->chain[1]->hash = $gumoChain->chain[1]->computeHash();

print_r($gumoChain);
var_dump($gumoChain->validateChain());