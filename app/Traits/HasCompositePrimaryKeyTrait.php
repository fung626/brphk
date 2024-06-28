<?php
namespace App\Traits;

// use Illuminate\Database\Eloquent\Builder;

trait HasCompositePrimaryKeyTrait
{

    public function getIncrementing()
    {
        return false;
    }

    /**
     * Get the value of the model's primary key.
     *
     * @return mixed
     */
    public function getKey()
    {
        $attributes = [];
        foreach ($this->getKeyName() as $key) {
            $attributes[$key] = $this->getAttribute($key);
        }
        return $attributes;
    }

    //Set the keys for a save update query.
    protected function setKeysForSaveQuery($query)
    { //edit Builder $query to $query

        foreach ($this->getKeyName() as $key) {
            // UPDATE: Added isset() per devflow's comment.
            if (isset($this->$key)) {
                $query->where($key, '=', $this->$key);
            } else {
                throw new Exception(__METHOD__ . 'Missing part of the primary key: ' . $key);
            }

        }
        return $query;
    }

    protected function getKeyForSaveQuery($keyName = null)
    {

        if (is_null($keyName)) {
            $keyName = $this->getKeyName();
        }

        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }

        return $this->getAttribute($keyName);
    }

    //Execute a query for a single record by ID.
    public static function find($ids, $columns = ['*'])
    {
        $me = new self;
        $query = $me->newQuery();

        foreach ($me->getKeyName() as $key) {
            $query->where($key, '=', $ids[$key]);
        }

        return $query->first($columns);
    }

    /**
     * Find a model by its primary key or throw an exception.
     *
     * @param mixed $ids
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function findOrFail($ids, $columns = ['*'])
    {
        $result = self::find($ids, $columns);
        if (!is_null($result)) {
            return $result;
        }
        throw (new ModelNotFoundException)->setModel(
            __CLASS__, $ids
        );
    }

    /**
     * Reload the current model instance with fresh attributes from the database.
     *
     * @return $this
     */
    public function refresh()
    {
        if (!$this->exists) {
            return $this;
        }
        $this->setRawAttributes(
            static::findOrFail($this->getKey())->attributes
        );
        $this->load(collect($this->relations)->except('pivot')->keys()->toArray());
        return $this;
    }

}