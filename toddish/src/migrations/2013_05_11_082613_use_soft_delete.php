<?php

use Illuminate\Database\Migrations\Migration;

class UseSoftDelete extends Migration {

    public function __construct()
    {
        // Get the prefix
        $this->prefix = Config::get('verify::prefix', '');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Bring to local scope
        $prefix = $this->prefix;

        // Add soft delete column
        Schema::table($this->prefix.'users', function($table)
        {
            $table->dateTime('deleted_at')->nullable()->index();
        });

        $users = DB::table($this->prefix.'users')
            ->where('deleted', 1)
            ->update([
                'deleted_at' => date('Y-m-d H:i:s')
            ]);

        Schema::table($this->prefix.'users', function($table)
        {
            $table->dropColumn('deleted');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Bring to local scope
        $prefix = $this->prefix;

        // Add soft delete column
        Schema::table($this->prefix.'users', function($table)
        {
            $table->boolean('deleted')->default(0);
        });

        $users = DB::table($this->prefix.'users')
            ->whereNotNull('deleted_at')
            ->update([
                'deleted' => 1
            ]);

        Schema::table($this->prefix.'users', function($table)
        {
            $table->dropColumn('deleted_at');
        });
    }

}