<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Pokemon</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f4; padding: 20px; }
        .container { max-width: 1100px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .filter-group { margin: 20px 0; display: flex; gap: 10px; align-items: center; }
        .btn-filter { padding: 8px 15px; border-radius: 20px; text-decoration: none; background: #eee; color: #333; font-size: 14px; border: 1px solid #ccc; }
        .btn-filter.active { background: #ef5350; color: white; border-color: #ef5350; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #ef5350; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        img { width: 80px; height: 80px; }
        .badge { background: #4caf50; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Daftar Pokemon di Database</h2>
    
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div class="filter-group">
            <strong>Filter Berat:</strong>
            <a href="<?= site_url('pokemon') ?>" class="btn-filter <?= empty($current_filter) ? 'active' : '' ?>">All</a>
            <a href="<?= site_url('pokemon?filter=light') ?>" class="btn-filter <?= $current_filter == 'light' ? 'active' : '' ?>">Light (100-150)</a>
            <a href="<?= site_url('pokemon?filter=medium') ?>" class="btn-filter <?= $current_filter == 'medium' ? 'active' : '' ?>">Medium (151-199)</a>
            <a href="<?= site_url('pokemon?filter=heavy') ?>" class="btn-filter <?= $current_filter == 'heavy' ? 'active' : '' ?>">Heavy (200+)</a>
        </div>
        <a href="<?= site_url('import-pokemon') ?>" style="color: blue; font-weight: bold;">[Update/Impor Data Baru]</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Gambar</th>
                <th>Nama</th>
                <th>Base Experience</th>
                <th>Ability</th> 
                <th>Berat (Unit)</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($pokemons)): ?>
                <?php foreach($pokemons as $p): ?>
                <tr>
                    <td><?= $p['id']; ?></td>
                    <td><img src="<?= $p['image_path']; ?>" alt="img"></td>
                    <td><strong><?= ucfirst($p['name']); ?></strong></td>
                    <td><?= $p['base_experience']; ?></td>
                    <td><small><?= $p['ability_names'] ?: '<em style="color:red">No Ability</em>'; ?></small></td>
                    <td><span class="badge"><?= $p['weight']; ?></span></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center; padding: 30px; color: #888;">
                        Tidak ada data Pokemon untuk kategori ini.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
