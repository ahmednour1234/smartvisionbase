<?php
namespace App\Repositories;

use App\Models\Package;

class PackageRepository
{
    public function all()
    {
        return Package::latest()->paginate(10);
    }

    public function find($id)
    {
        return Package::findOrFail($id);
    }

    public function create(array $data)
    {
        return Package::create($data);
    }

    public function update($id, array $data)
    {
        $package = $this->find($id);
        $package->update($data);
        return $package;
    }

    public function delete($id)
    {
        return Package::destroy($id);
    }

    public function toggleActive($id)
    {
        $package = $this->find($id);
        $package->active = ! $package->active;
        $package->save();
        return $package;
    }
}
