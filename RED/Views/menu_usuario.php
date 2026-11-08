<DIV CLASS="profile-menu" id="profileMenu">

            <div class="profile-menu-header">

                <div class="profile-menu-avatar" style="overflow:hidden;"><?= avatarUsuario("menuAvatar","menuIniciales",$inicialesHeader,$v) ?></div>

                <div class="profile-menu-name">
                    @<?= h($tagHeader) ?>
                </div>

            </div>

            <button class="profile-menu-verperfil" onclick="window.location.href='perfil.php'">
                <i class="ion-md-person"></i> Ver perfil
            </button>

            <div class="profile-menu-divider"></div>

            <ul class="profile-menu-list">

                <li>
                    <i class="ion-md-help-circle"></i> Ayuda
                </li>

                <li onclick="toggleAccessMenu(true)">
                    <i class="ion-md-options"></i> Accesibilidad
                </li>

                <li class="profile-menu-logout" onclick="window.location.href='login.php'">
                    <i class="ion-md-log-out"></i> Cerrar sesión
                </li>

            </ul>

        </DIV>

        <DIV CLASS="access-menu" id="accessMenu">

            <div class="access-menu-header">

                <button class="access-back" onclick="toggleAccessMenu(false)">
                    <i class="ion-md-arrow-back"></i>
                </button>

                <span>Pantalla y accesibilidad</span>

            </div>

            <div class="access-section">

                <div class="access-section-title">
                    <i class="ion-md-moon"></i> Apariencia
                </div>

                <div class="access-options">

                    <button class="access-chip" id="themeLight" onclick="setTheme('light')">
                        <i class="ion-md-sunny"></i> Claro
                    </button>

                    <button class="access-chip active" id="themeDark" onclick="setTheme('dark')">
                        <i class="ion-md-moon"></i> Oscuro
                    </button>

                </div>

            </div>

            <div class="access-section">

                <div class="access-section-title">
                    <i class="ion-md-text"></i> Tamaño de letra
                </div>

                <div class="access-fontsize-control">

                    <button class="access-font-btn" id="fontDecrease">
                        A-
                    </button>

                    <span class="access-font-value" id="fontValue">
                        100%
                    </span>

                    <button class="access-font-btn" id="fontIncrease">
                        A+
                    </button>

                </div>

            </div>

            <div class="access-section">

                <div class="access-section-title-row">

                    <div class="access-section-title">
                        <i class="ion-md-contrast"></i> Alto contraste
                    </div>

                    <label class="access-switch">

                        <input type="checkbox" id="contrastToggle">

                        <span class="access-switch-slider"></span>

                    </label>

                </div>

                <p class="access-desc">
                    Aumenta el contraste de los textos para facilitar la lectura.
                </p>

            </div>

        </DIV>