<?php

namespace App\oldModels;

use http\Exception\InvalidArgumentException;

class CompteBancaire
{
    private string $titulaire;
    private float $solde=80;
    /**
     * @property Operation[]
     */
    private array $operations;

    public function __construct(string $titulaire,float $solde=0){
        $this->titulaire=$titulaire;
        $this->solde=($solde<0)?0:$solde;
        $this->operations=[];
    }

    private function verifierMontant(float $montant){
        if($montant<0){
            throw new InvalidArgumentException("le montant doit être strictement positif");
        }
    }

    public function deposer(string $intitule, float $montant){
        $this->verifierMontant($montant);
        $this->solde += $montant;
        $this->operations[]=Operation::credit($intitule, $montant);
    }

    /**
     * @return array
     */
    public function getOperations(): array
    {
        return $this->operations;
    }

    public function getSolde(): float
    {
        return $this->solde;
    }

    public function __toString(): string
    {
        return "$this->titulaire:$this->solde";
    }
}