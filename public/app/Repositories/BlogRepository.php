<?php

namespace App\Repositories;

use App\Models\Blog;

class BlogRepository
{
    public function all()
    {
        return Blog::latest()->paginate(10);
    }

    public function find($id)
    {
        return Blog::findOrFail($id);
    }

    public function create(array $data)
    {
        return Blog::create($data);
    }

    public function update($id, array $data)
    {
        $blog = $this->find($id);
        $blog->update($data);
        return $blog;
    }

    public function delete($id)
    {
        $blog = $this->find($id);
        return $blog->delete();
    }

    public function toggleActive($id)
    {
        $blog = $this->find($id);
        $blog->active = !$blog->active;
        $blog->save();
    }
}
