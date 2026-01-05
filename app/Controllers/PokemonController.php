<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class PokemonController extends BaseController
{
public function index()
{
    $db = \Config\Database::connect();
    $builder = $db->table('pokemons p');
    $builder->select('p.*, GROUP_CONCAT(a.name SEPARATOR ", ") as ability_names');
    $builder->join('pokemon_abilities pa', 'pa.pokemon_id = p.id', 'left');
    $builder->join('abilities a', 'a.id = pa.ability_id', 'left');

    // 1. Filter agar weight tidak kosong (NULL atau 0)
    $builder->where('p.weight >', 0);

    // 2. Logika Filter Berat berdasarkan Input User
    $filter = $this->request->getGet('filter');
    if ($filter == 'light') {
        $builder->where('p.weight >=', 100)->where('p.weight <=', 150);
    } elseif ($filter == 'medium') {
        $builder->where('p.weight >=', 151)->where('p.weight <=', 199);
    } elseif ($filter == 'heavy') {
        $builder->where('p.weight >=', 200);
    }

    $builder->groupBy('p.id');
    $builder->orderBy('p.id', 'ASC');
    
    $data['pokemons'] = $builder->get()->getResultArray();
    $data['current_filter'] = $filter; 

    return view('pokemon_list', $data);
}

    
public function import()
{
    ini_set('memory_limit', '512M');
    set_time_limit(0); 

    $db = \Config\Database::connect();
    $client = \Config\Services::curlrequest();
    
    $successCount = 0;

    try {
        $mainResponse = $client->get("https://pokeapi.co/api/v2/pokemon/?offset=0&limit=400", [
            'verify' => false, 'timeout' => 30
        ]);

        $listData = json_decode($mainResponse->getBody(), true);
        $pokemons = $listData['results'];

        foreach ($pokemons as $item) {
            try {
                usleep(50000); 
                $detailResponse = $client->get($item['url'], ['verify' => false, 'timeout' => 10]);

                if ($detailResponse->getStatusCode() === 200) {
                    $detail = json_decode($detailResponse->getBody(), true);

                    if ($detail['weight'] >= 100) {
                        // 1. Simpan/Update Pokemon
                        $pokemonData = [
                            'id'              => $detail['id'],
                            'name'            => $detail['name'],
                            'base_experience' => $detail['base_experience'] ?? 0,
                            'weight'          => $detail['weight'],
                            'image_path'      => $detail['sprites']['front_default'] ?? null,
                        ];
                        $db->table('pokemons')->upsert($pokemonData);

                        // 2. Proses Abilities
                        foreach ($detail['abilities'] as $ab) {
                            $abilityName = $ab['ability']['name'];
                            
                            // Ambil ID Ability dari URL API 
                            $urlParts = explode('/', rtrim($ab['ability']['url'], '/'));
                            $abilityId = end($urlParts);

                            // Simpan ke tabel abilities jika belum ada
                            $db->table('abilities')->upsert([
                                'id'   => $abilityId,
                                'name' => $abilityName
                            ]);

                            // 3. Simpan Relasi ke pokemon_abilities
                            $db->table('pokemon_abilities')->upsert([
                                'pokemon_id'   => $detail['id'],
                                'ability_id' => $abilityId
                            ]);
                        }
                        $successCount++;
                    }
                }
            } catch (\Exception $e) { continue; }
        }

        return $this->response->setJSON(['status' => 'success', 'message' => "Berhasil impor $successCount Pokemon beserta kemampuannya."]);

    } catch (\Exception $e) {
        return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'error' => $e->getMessage()]);
    }
}
}
