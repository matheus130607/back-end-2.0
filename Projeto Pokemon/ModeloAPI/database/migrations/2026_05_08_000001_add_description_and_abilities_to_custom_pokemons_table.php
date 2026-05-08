<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('custom_pokemons', function (Blueprint $table) {
            if (!Schema::hasColumn('custom_pokemons', 'description')) {
                $table->text('description')->nullable()->after('weight');
            }

            if (!Schema::hasColumn('custom_pokemons', 'abilities')) {
                $table->json('abilities')->nullable()->after('ability');
            }
        });
    }

    public function down(): void
    {
        Schema::table('custom_pokemons', function (Blueprint $table) {
            if (Schema::hasColumn('custom_pokemons', 'abilities')) {
                $table->dropColumn('abilities');
            }

            if (Schema::hasColumn('custom_pokemons', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
