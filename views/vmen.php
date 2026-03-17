<?php
// Incluye el controlador que obtiene los datos del menú
include("controllers/cmen.php"); 
?>

<?php if ($datm && count($datm) > 0) { ?>

<style>
    #navbar:not(:hover) .navbar-item-inner-icon-wrapper {
        width: 100% !important;
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    
    /* Asegura que el icono dentro del wrapper no tenga desplazamientos */
    .navbar-item-inner-icon-wrapper i {
        width: auto !important;
        margin: 0 !important;
        text-align: center !important;
    }

    /* Ajuste para el ítem de usuario en modo colapsado */
    #navbar:not(:hover) .navbar-info-item {
        padding: 12px 0 !important;
        justify-content: center !important;
    }

    #navbar:not(:hover) .navbar-info-item .user-details-wrapper {
        display: none !important;
    }
</style>

<nav id="navbar">
    
    <div class="navbar-logo flexbox-left">
        <div class="navbar-item-inner flexbox-left"> 
            <div class="navbar-item-inner-icon-wrapper flexbox-col">
                <i class="fa-solid fa-house" title="Inicio"></i>
            </div>
            <span class="link-text">Inicio</span>
        </div>
    </div>

    <div class="navbar-search">
        <input type="text" id="menu-search" placeholder="Buscar en menú..." autocomplete="off">
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
    </div>

    <div class="scrollable-menu-items" id="scrollable-menu">
        <ul class="navbar-top-items" id="menu-items-list">
            <?php 
            foreach ($datm as $dm) { 
                $class_active = ($dm['idpag'] == $pg) ? 'active' : '';
            ?>
                <li class="navbar-item flexbox-left <?= $class_active; ?>" 
                    data-menu-text="<?= strtolower(htmlspecialchars($dm['nompag'])); ?>">
                    <a class="navbar-item-inner flexbox-left" href="home.php?pg=<?= $dm['idpag']; ?>">
                        <div class="navbar-item-inner-icon-wrapper flexbox-col">
                            <i class="<?= $dm['icopag']; ?>"></i>
                        </div>
                        <span class="link-text"><?= $dm['nompag']; ?></span>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>
    
    <ul class="navbar-bottom-items"> 
        <li class="navbar-info-item flexbox-left" style="padding: 12px 16px; display: flex; align-items: center; gap: 10px; color: white; cursor: default;">
            <div class="navbar-item-inner-icon-wrapper flexbox-col" style="min-width: 30px; text-align: center;">
                <i class="fa-solid fa-user"></i> 
            </div>
            <div class="user-details-wrapper link-text" style="display: flex; flex-direction: column; line-height: 1.2;">
                <span style="font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 140px; font-size: 0.85rem;">
                    <?= $_SESSION['nomusu']; ?>
                </span>
                <small style="font-size: 0.65rem; opacity: 0.7; text-transform: uppercase;">
                    <?= $_SESSION['nomper']; ?>
                </small>
            </div>
        </li>
        
        <li class="navbar-item flexbox-left">
            <a class="navbar-item-inner flexbox-left" href="index.php">
                <div class="navbar-item-inner-icon-wrapper flexbox-col">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </div>
                <span class="link-text">Cerrar Sesión</span>
            </a>
        </li>
    </ul>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('menu-search');
    const menuItems = document.querySelectorAll('#menu-items-list .navbar-item');
    const scrollableContainer = document.getElementById('scrollable-menu');

    searchInput.addEventListener('input', function () {
        const query = this.value.trim().toLowerCase();
        let visibleCount = 0;

        menuItems.forEach(item => {
            const text = item.getAttribute('data-menu-text');
            if (query === '' || text.includes(query)) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        let noResults = document.getElementById('no-results-msg');
        if (!noResults && query !== '' && visibleCount === 0) {
            noResults = document.createElement('div');
            noResults.id = 'no-results-msg';
            noResults.textContent = 'No se encontraron resultados';
            noResults.style.padding = '12px 16px';
            noResults.style.color = '#aaa';
            noResults.style.fontStyle = 'italic';
            noResults.style.fontSize = '0.9rem';
            scrollableContainer.insertBefore(noResults, scrollableContainer.firstChild);
        } else if (noResults && (query === '' || visibleCount > 0)) {
            noResults.remove();
        }
    });

    searchInput.addEventListener('blur', function () {
        if (this.value === '') {
            this.value = '';
        }
    });
});
</script>

<?php } ?>
