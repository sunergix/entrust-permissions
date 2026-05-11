<?php echo '<?php' ?>

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // Create table for storing roles
        Schema::create('{{ $rolesTable }}', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Create table for associating roles to users (Many-to-Many)
        Schema::create('{{ $roleUserTable }}', function (Blueprint $table) {
            $table->unsignedBigInteger('{{ $userForeignKey }}');
            $table->unsignedBigInteger('{{ $roleForeignKey }}');

            $table->foreign('{{ $userForeignKey }}')->references('{{ $userKeyName }}')->on('{{ $usersTable }}')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('{{ $roleForeignKey }}')->references('id')->on('{{ $rolesTable }}')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['{{ $userForeignKey }}', '{{ $roleForeignKey }}']);
        });

        // Create table for storing permissions
        Schema::create('{{ $permissionsTable }}', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Create table for associating permissions to roles (Many-to-Many)
        Schema::create('{{ $permissionRoleTable }}', function (Blueprint $table) {
            $table->unsignedBigInteger('{{ $permissionForeignKey }}');
            $table->unsignedBigInteger('{{ $roleForeignKey }}');

            $table->foreign('{{ $permissionForeignKey }}')->references('id')->on('{{ $permissionsTable }}')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('{{ $roleForeignKey }}')->references('id')->on('{{ $rolesTable }}')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['{{ $permissionForeignKey }}', '{{ $roleForeignKey }}']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('{{ $permissionRoleTable }}');
        Schema::dropIfExists('{{ $permissionsTable }}');
        Schema::dropIfExists('{{ $roleUserTable }}');
        Schema::dropIfExists('{{ $rolesTable }}');
    }
};
