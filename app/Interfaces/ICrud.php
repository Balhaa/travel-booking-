<?php
/**
 * Interface ICrud
 * Kontrak untuk operasi standar database (Create, Read, Update, Delete, Search).
 */

namespace App\Interfaces;

interface ICrud {
    /**
     * Mengambil semua data.
     * @return array
     */
    public function index();

    /**
     * Mengambil satu data berdasarkan ID.
     * @param int $id
     * @return mixed
     */
    public function show($id);

    /**
     * Menambahkan data baru.
     * @param array $data
     * @return bool
     */
    public function store($data);

    /**
     * Mengupdate data yang ada.
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data);

    /**
     * Menghapus data.
     * @param int $id
     * @return bool
     */
    public function destroy($id);

    /**
     * Mencari data berdasarkan keyword.
     * @param string $keyword
     * @return array
     */
    public function search($keyword);
}
