<?php
    $products = $products ?? array();
    $categories = $categories ?? array();
    $vendors = $vendors ?? array();
    
?>

<div class="formWrapper">
    <form class="searchForm" method="Get">
        <input type="hidden" name="c" value="products" />
        <input type="hidden" name="a" value="search" />

        <!-- hidden inputs to preserve get parameters of filter form -->
        <?php foreach($vendors as $vendor) : ?>
            <?php if(isset($_GET['ven'.$vendor->id])) : ?>
                <input type="hidden" name="ven<?=$vendor->id?>" value="<?=$vendor->id?>"/>
            <?php endif ?>
        <?php endforeach ?>

        <?php foreach($categories as $category) : ?>
            <?php if(isset($_GET['cat'.$category->id])) : ?>
                <input type="hidden" name="cat<?=$category->id?>" value="<?=$category->id?>"/>
            <?php endif ?>
        <?php endforeach ?>

        <?php if(isset($_GET['minPrice'])) : ?>
            <input type="hidden" name="minPrice" value="<?= $_GET['minPrice']?>">
        <?php endif ?>

        <?php if(isset($_GET['maxPrice'])) : ?>
            <input type="hidden" name="maxPrice" value="<?= $_GET['maxPrice']?>">
        <?php endif ?>

        <?php if(isset($_GET['sort'])) : ?>
            <input type="hidden" name="sort" value="<?= $_GET['sort']?>">
        <?php endif ?>

        <input id="search" name="s" type="search" placeholder="Suchbegriff eingeben..." value="<?= htmlspecialchars($_GET['s'] ?? '')?>"/>
        <input id="searchSubmit" type="submit" value="S">
    </form>
</div>

<div class="formWrapper">
    <label for="filterToggle" class="filterToggleLabel">Filter<span class="dropIcon">&#9776;</span></label>
    <input type="checkbox" id="filterToggle" class="filterToggle">

    <form class="filterForm" method="Get">
    <input type="hidden" name="c" value="products" />
            <input type="hidden" name="a" value="search" />
        
        <?php if(isset($_GET['s'])) : ?>
            <input type="hidden" name="s" value="<?= $_GET['s']?>">
        <?php endif ?>

        <fieldset>
            <legend>Nach Marke filtern</legend>
                <?php foreach($vendors as $vendor) : ?>
                    <label for="vendor<?=$vendor->id?>"><?= $vendor->vendorName ?>
                    <input type="checkbox" name="ven<?=$vendor->id?>" id="vendor<?=$vendor->id?>" value="<?=$vendor->id?>" <?=isset($_GET['ven' . $vendor->id]) ? 'checked' : ''?>/></label>
                <?php endforeach ?>
        </fieldset>

        <fieldset id="categorySort">
            <legend>Nach Kategorie filtern</legend>
                <?php foreach($categories as $category) : ?>
                    <label for="category<?=$category->id?>"><?= $category->categoryName ?>
                    <input type="checkbox" name="cat<?=$category->id?>" id="category<?=$category->id?>" value="<?=$category->id?>" <?=isset($_GET['cat' . $category->id]) ? 'checked' : ''?>/></label>
                <?php endforeach ?>
        </fieldset>

        <fieldset>
            <legend>Preisspanne festlegen</legend>
            <label for="minPrice">Min</label>
            <input type="number" min="1" step="any" id="minPrice" name="minPrice" value="<?= $_GET['minPrice'] ?? '' ?>"/>
            
            <label for="maxPrice">Max</label>
            <input type="number" min="1" step="any" id="maxPrice" name="maxPrice" value="<?= $_GET['maxPrice'] ?? '' ?>"/>
        </fieldset>

        <label for="sort"></label>
        <select id="sort" name=sort>
            <option value="none"      <?= (isset($_GET['sort']) && $_GET['sort'] == 'none') ? ' selected' : ''?>>Ohne Sortierung</option>

            <option value="nameASC"   <?= (isset($_GET['sort']) && $_GET['sort'] == 'nameASC') ? ' selected' : ''?>>Name aufsteigend</option>
            <option value="nameDESC"  <?= (isset($_GET['sort']) && $_GET['sort'] == 'nameDESC') ? ' selected' : ''?>>Name absteigend</option>

            <option value="priceASC"  <?= (isset($_GET['sort']) && $_GET['sort'] == 'priceASC') ? ' selected' : ''?>>Preis aufsteigend</option>
            <option value="priceDESC" <?= (isset($_GET['sort']) && $_GET['sort'] == 'priceDESC') ? ' selected' : ''?>>Preis absteigend</option>

            <option value="dateDESC" <?= (isset($_GET['sort']) && $_GET['sort'] == 'dateDESC') ? ' selected' : ''?>>Neueste zuerst</option>
            <option value="dateASC" <?= (isset($_GET['sort']) && $_GET['sort'] == 'dateASC') ? ' selected' : ''?>>Älteste zuerst</option>

        </select>
        <br>

        <input type="submit" value="Filter anwenden">
    </form>
</div>

<?php if(empty($products)) :?>
    <p>Für Ihre Suche wurden leider keine Treffer erzielt!</p>
<?php endif ?>

<section class="cards"> 
    <?php foreach ($products as $product) : ?>
        <?php include(VIEWSPATH . DIRECTORY_SEPARATOR . 'viewAssets' . DIRECTORY_SEPARATOR . 'productCard.php') ?>
    <?php endforeach ?>
</section>




<br>
<div class="pagesList">
    <!-- Button to return to first product list -->
    <a href="index.php?<?=$getString?>&page=<?=1?>">&laquo;</a>
    
    <!-- Buttons to go to specific product page within range -->
    <?php for($i = $startIndex-2; $i <= $startIndex+PRODUCT_LIST_RANGE; $i++) : ?>
        <?php if($i > 0 && $i <= $numberOfPages) : ?>
            <a href="index.php?<?=$getString?>&page=<?=$i?>" <?= ($i == $currentPage) ? 'class="active"' : ''?>><?=$i?></a>
        <? endif ?>
    <? endfor ?>

    <!-- Button to go to last product List -->
    <a href="index.php?<?=$getString?>&page=<?=$numberOfPages?>">&raquo;</a>
</div>
<script src="<?=JAVASCRIPTPATH . 'products' . DIRECTORY_SEPARATOR . 'shortenSearch.js'?>"></script>