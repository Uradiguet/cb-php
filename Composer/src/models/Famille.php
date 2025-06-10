<?php

/**
 * Created by Reliese Model.
 */

namespace App\models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Famille
 * 
 * @property int $id
 * @property string|null $libelle
 * @property bool|null $typeOperation
 * @property int|null $idUtilisateur
 * 
 * @property Utilisateur|null $utilisateur
 * @property Collection|Operation[] $operations
 *
 * @package App\models
 */
class Famille extends Model
{
	protected $table = 'famille';
	public $timestamps = false;

	protected $casts = [
		'typeOperation' => 'bool',
		'idUtilisateur' => 'int'
	];

	protected $fillable = [
		'libelle',
		'typeOperation',
		'idUtilisateur'
	];

	public function utilisateur()
	{
		return $this->belongsTo(Utilisateur::class, 'idUtilisateur');
	}

	public function operations()
	{
		return $this->hasMany(Operation::class, 'idFamille');
	}
}
