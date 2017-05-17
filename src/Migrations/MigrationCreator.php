<?php

namespace Vinelab\NeoEloquent\Migrations;

use Illuminate\Database\Migrations\MigrationCreator as IlluminateMigrationCreator;

class MigrationCreator extends IlluminateMigrationCreator
{
    /**
     * Populate the place-holders in the migration stub.
     *
     * @param string $name
     * @param string $stub
     * @param string $label
     *
     * @return string
     */
    protected function populateStub($name, $stub, $label)
    {
        $stub = str_replace('{{class}}', studly_case($name), $stub);

        // Here we will replace the label place-holders with the label specified by
        // the developer, which is useful for quickly creating a labels creation
        // or update migration from the console instead of typing it manually.

        if (!is_null($label)) {
            $stub = str_replace('{{label}}', $label, $stub);
        }

        return $stub;
    }

    /**
     * {@inheritDoc}
     */
    public function getStubPath()
    {
        return __DIR__.'/stubs';
    }

    public function stubPath()
    {
        return __DIR__.'/stubs';
    }
    public function create($name, $path, $table = null, $create = false)
    {
        $this->ensureMigrationDoesntAlreadyExist($name);

        // First we will get the stub file for the migration, which serves as a type
        // of template for the migration. Once we have those we will populate the
        // various place-holders, save the file, and run the post create event.
        if(is_null($table)){
            $table = preg_replace('/^Create/i', '', studly_case($name));
            $table = preg_replace('/Label$/i', '', $table);
        }
        $stub = $this->getStub($table, $create);

        $this->files->put(
            $path = $this->getPath($name, $path),
            $this->populateStub($name, $stub, $table)
        );

        // Next, we will fire any hooks that are supposed to fire after a migration is
        // created. Once that is done we'll be ready to return the full path to the
        // migration file so it can be used however it's needed by the developer.
        $this->firePostCreateHooks();

        return $path;
    }

    protected function getStub($table, $create)
    {

        if (is_null($table)) {
            return $this->files->get($this->stubPath().'/blank.stub');
        }

        // We also have stubs for creating new tables and modifying existing tables
        // to save the developer some typing when they are creating a new tables
        // or modifying existing tables. We'll grab the appropriate stub here.
        else {
            $stub = $create ? 'create.stub' : 'update.stub';

            return $this->files->get($this->stubPath()."/{$stub}");
        }
    }

}
