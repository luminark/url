<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('urls', function (Blueprint $table) {
            $table->string('uri')->primary();
            $table->string('redirects_to')->nullable()->index();
            // Nullable morphs_to resource
            $table->integer('resource_id')->unsigned()->nullable();
            $table->string('resource_type')->nullable();
            $table->timestamp('created_at');
            
            $table->foreign('redirects_to')
                ->references('uri')->on('urls')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('urls');
    }
}
