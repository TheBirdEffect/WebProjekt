<?php $currentPosition = $currentPosition ?? ''; ?>

<nav>
    <label for="navToggle" id="navToggleLabel">Navigation<span class="dropIcon">&#9776;</span></label>
    <input type="checkbox" id="navToggle">

    <ul class="mainNav clearfix">
        <li <?= ($currentPosition == 'index') ? 'class="active"' : '' ?>><a href="?c=pages&a=index" >Startseite</a></li>

        <!-- Products -->
        <li><a>Produkte
                <span class="dropIcon">▾</span>
                <label title="toggle dropDown" class="dropIcon" for="dropToggle01">▾</label>
            </a>
            <input type="checkbox" id="dropToggle01">
            <ul class="subNav">
                <li><a href="#">Alle Artikel</a></li>
                <li><a href="#">Kategorien</a>
                <li><a href="#">Suche</a></li>
            </ul>
        </li>

        <li class="right"><a href="#">Warenkorb</a></li>
        <li <?= ($currentPosition == 'impressum') ? 'class="right active"' : 'class="right"'?>><a href="?c=pages&a=impressum">Impressum</a></li>
        <li class="right"><a href="#">Login</a></li>
        
        <!-- Administration -->
        <li class="right"><a>Administration
                <span class="dropIcon">▾</span>
                <label title="toggle dropDown" class="dropIcon" for="dropToggle02">▾</label>
            </a>
            <input type="checkbox" id="dropToggle02">
            <ul class="subNav">
                <li><a href="#">Mein Konto</a></li>
                <li><a href="#">Produkte verwalten</a>
                <li><a href="#">Benutzer verwalten</a></li>
            </ul>
        </li>
    </ul>
</nav>