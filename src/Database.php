<?php 
namespace App;
use Kreait\Firebase;

class Database 
{
    /**
     * @var Firebase
     */
    private $firebase;

    public function __construct(Firebase $firebase)
    {
        $this->firebase = $firebase;
    }
    public function allchildof($uidparent){

        $reference = 'users/'.$uidparent.'/enfant';
        $snapshot = $this->firebase->getDatabase()->getReference($reference)->getSnapshot();
        
        
        return $snapshot->getValue();
    }
    public function infochild($uidchild){

        $reference = 'users/'.$uidchild;
        $snapshot = $this->firebase->getDatabase()->getReference($reference)->getSnapshot();
        $key =  $snapshot->getKey();

        $value = $snapshot->getValue();
        if($value){
            $enfant = ["key"=>$key,"value"=>$snapshot->getValue()];
            return $enfant;
        }else{
            return null;
        }
    }
    
    public function getTrajet(string $uid){
        $reference = 'users/'.$uid.'/position';
        $snapshot = $this->firebase->getDatabase()->getReference($reference)->getSnapshot();

        

        $value = $snapshot->getValue();
        if($value){
            
            return $value;
        }else{
            return null;
        }
    }

    
    public function addchild($uidparent,$uidchild){
        
        $reference = "users/".$uidparent.'/enfant';
        $snapshot = $this->firebase->getDatabase()->getReference($reference)->getSnapshot();

        $newPostKey = $snapshot->getReference()->push()->getKey();
        


        $updates = [
            $newPostKey => $uidchild
        ];
        
        $snapshot->getReference() // this is the root reference
           ->update($updates);
    }

    public function updatechild($uidchild,$enfant_ch){
        $reference = "users/".$uidchild;
        $snapshot = $this->firebase->getDatabase()->getReference($reference)->getSnapshot();

        $newPostKey = $snapshot->getReference()->push()->getKey();
        
        $updates = [
            $uidchild => $enfant_ch
        ];
        
        $snapshot->getReference() 
           ->update($updates);
    }

    public function deletechild($uidparent,$uidchild){


        $reference = "users/".$uidparent.'/enfant';
        $snapshot = $this->firebase->getDatabase()->getReference($reference)->getSnapshot();

        $value = $snapshot->getValue();

        foreach ($value as $key => $e) {
            

            if($e == $uidchild){
                $id = $reference."/".$key."/".$e;
                $this->firebase->getDatabase()->getReference($reference."/".$key)->remove();
            }
            
        }
        
        
    }
    
}

?>