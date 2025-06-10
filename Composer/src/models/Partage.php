<?php

/**
 * Created by Reliese Model.
 */

namespace App\models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Partage
 * 
 * @property int $idUtilisateur
 * @property int $idCompte
 * @property bool|null $ecriture
 * 
 * @property Utilisateur $utilisateur
 * @property Compte $compte
 *
 * @package App\models
 */
class Partage extends Model
{
	protected $table = 'partage';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'idUtilisateur' => 'int',
		'idCompte' => 'int',
		'ecriture' => 'bool'
	];

	protected $fillable = [
		'ecriture'
	];

	public function utilisateur()
	{
		return $this->belongsTo(Utilisateur::class, 'idUtilisateur');
	}

	public function compte()
	{
		return $this->belongsTo(Compte::class, 'idCompte');
	}
}
