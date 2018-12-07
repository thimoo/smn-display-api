<?php

namespace App\Importers;

use App\Data;
use App\Value;
use App\Profile;
use App\Events\NewValues;
use App\Events\CheckProfiles;
use App\Events\BeforeValuesInserted;
use App\Parsers\DataSets\DataSet;

class Importer
{
    /**
     * Stores the data set to import
     *
     * @var App\Parsers\DataSets\DataSet
     */
    protected $dataSet;

    /**
     * Load the data set and store it
     *
     * @param  DataSet $dataSet the data set to import
     * @return Importer         $this
     */
    public function load(DataSet $dataSet)
    {
        $this->dataSet = $dataSet;

        return $this;
    }

    /**
     * Compute the process to insert all values prensent
     * in the data set and check all constraints attached
     *
     * @return Importer         $this
     */
    public function import()
    {
        $this->BeforeValuesInserted()
             ->insertValues()
             ->checkProfiles();

        return $this;
    }

    /**
     * Browse the complete data set loaded and create
     * a new Value based on the retreived informations
     * The new value has passed to a new "NewValues" event
     *
     * @return Importer         $this
     */
    protected function insertValues()
    {
        $output = new \Symfony\Component\Console\Output\ConsoleOutput();
        $output->writeln("<info>Start : ".date("H:i:s")."</info>");

        $datas=Data::all();

        $currentProfile=null;
        $this->dataSet->resetCursors();
        while ($this->dataSet->hasNextValue())
        {
            list($profile, $data, $value, $time) = $this->dataSet->getNextValue();

            //est-ce que c'est un nouveau profile
            if($currentProfile!=$profile)
            {
              //Verifie que ce n'est pas le premier profile
              if($currentProfile!=null)
              {
                $output->writeln("<info>Insert : ".$currentProfile." (".count($values).")</info>");
                //Ajouter toutes les données d'un profile
                event(new NewValues($values));
                //reset le profile.
                $values = array();
              }
              //définition du profile courant
              $currentProfile=$profile;
            }

            if(in_array($values->data_code, $datas)) {

              $values[] = new Value([
                  'profile_stn_code' => $profile,
                  'data_code' => $data,
                  'date' => $time,
                  'value' => $value,
                  'tag' => null,
              ]);
            }
        }
        //ajout toutes les données du dernier profile
        $output->writeln("<info>Insert : ".$currentProfile." (".count($values).")</info>");
        event(new NewValues($values));
        $output->writeln("<info>out : ".date("H:i:s")."</info>");

        return $this;
    }

    /**
     * Call the "ValueInserterd" event to fire the
     * post checks
     *
     * @return Importer          $this
     */
    protected function BeforeValuesInserted()
    {
        event(new BeforeValuesInserted);

        return $this;
    }

    /**
     * Browse all profiles present in the data set and
     * retreive it form the database to fire the "CheckProfiles"
     * event. CheckProfile verify if the online field on the profile
     * must be updated
     *
     * @return Importer           $this
     */
    protected function checkProfiles()
    {
        $this->dataSet->resetCursors();
        while ($this->dataSet->hasNextProfile())
        {
            $p = $this->dataSet->getNextProfile();
            $profile = Profile::find($p);
            event(new CheckProfiles($profile));
        }

        return $this;
    }
}
