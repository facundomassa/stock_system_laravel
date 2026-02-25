{ pkgs, ... }: {
  channel = "stable-24.05";
  packages = [
    pkgs.php83
    pkgs.php83Packages.composer
    pkgs.nodejs_20
    pkgs.mysql80
  ];
  
  services.mysql = {
    enable = true;
    package = pkgs.mysql80;
  };

  idx = {
    extensions = [
      "svelte.svelte-vscode"
      "vue.volar"
      "onecentlin.laravel5-snippets"
      "bmewburn.vscode-intelephense-client"
    ];

    workspace = {
      onCreate = {
        # 1. Instalamos dependencias
        # 2. Creamos la DB manualmente con un comando de shell
        setup = ''
          composer install
          npm install
          cp .env.example .env
          php artisan key:generate
          mysql -u root -e "CREATE DATABASE IF NOT EXISTS laravel_db;"
        '';
      };
    };

    previews = {
      enable = true;
      previews = {
        web = {
          command = [
            "php" "artisan" "serve" "--port" "$PORT" "--host" "0.0.0.0"
          ];
          manager = "web";
        };
      };
    };
  };
}