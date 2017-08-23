<div class="manufacturer-info">
    <h1><?php echo $manufacturer->name; ?></h1>
</div>

<div class="toolbar toolbar-top clearfix" role="toolbar">
    <div class="sorter-container">
        <div class="amount">
            <p><?php echo count($products); ?> Items</p>
        </div>
        <div class="limiter">
            <label for="limit-top">Show</label>
            <select id="limit-top" name="limit">
                <?php
                foreach ($limitOptions as $limitLabel => $limitOption) {
                    $selected = $limit == $limitOption ? 'selected="selected"' : '';
                    echo '<option value="?limit=' . $limitOption . '" ' . $selected . '>' . $limitLabel . '</option>';
                }
                ?>
            </select>
            <span class="per-page">per page</span>
        </div><!-- /.limiter -->
        <div class="sort-by">
            <label for="sortby-top">Sort By</label>
            <select id="sortby-top" name="sortby">
                <?php foreach ($sortOptions as $sort => $label) : ?>
                    <?php $selected = $order == $sort ? 'selected="selected"' : ''; ?>
                    <option <?= $selected; ?>
                        value="?limit=<?php echo $limit; ?>&amp;order=<?php echo $sort; ?>"><?php echo $label; ?></option>
                    <?php endforeach; ?>
            </select>
        </div><!-- /.sort-by -->
        <div class="view-mode">
            <span class="view-mode-label">Display as:</span>
            <span class="view-mode-btn">
                <?php if ('grid' === $mode): ?>
                    <span class="grid" title="Grid"><i class="glyphicon glyphicon-th" style="font-size:18px"></i></span>
                    <a href="?mode=list" class="list" data-toggle="view" role="button" title="List" rel="nofollow"
                       class="btn-list"><i class="glyphicon glyphicon-th-list" style="font-size:18px"></i></a>
                   <?php elseif ('list' === $mode) : ?>
                    <a href="?mode=grid" class="grid" data-toggle="view" role="button" title="Grid" rel="nofollow"
                       class="btn-grid"><i class="glyphicon glyphicon-th" style="font-size:18px"></i></a>
                    <span class="list" title="List" class="btn-list"><i class="glyphicon glyphicon-th-list"
                                                                        style="font-size:18px"></i></span>
                    <?php else : ?>
                    <a href="?mode=grid" class="grid" data-toggle="view" role="button" title="Grid" rel="nofollow"
                       class="btn-grid"><i class="glyphicon glyphicon-th" style="font-size:18px"></i></a>
                    <a href="?mode=list" class="list" data-toggle="view" role="button" title="List" rel="nofollow"
                       class="btn-list"><i class="glyphicon glyphicon-th-list" style="font-size:18px"></i></a>
                   <?php endif; ?>
            </span>
        </div><!-- /.view-mode -->
    </div><!-- /.sorter -->
</div>

<div class="product-container <?php echo $mode; ?>">
    <?php if (count($products) < 1) : ?>
        <p>This category has no products</p>
    <?php else : ?>
        <?php foreach (new \LimitIterator($products, $offset, $limit) as $product) : ?>
            <div class="product-holder">
                <div class="left-block">
                    <?php if (count($product->getProductImages()) > 0) : ?>
                        <div class="img-box">
                            <a href="/product/item/<?php echo Helpers\Stringify::slugify($product->slug); ?>">
                                <?php if ($product->getCoverProductImage() instanceof \library\Product\Image) : ?>
                                    <img src="<?php echo $product->getCoverProductImage()->getThumb(); ?>"
                                         title="<?php echo $product->name; ?>" alt="<?php echo $product->name; ?>"/>
                                     <?php elseif ($product->getFirstProductImage() instanceof \library\Product\Image) : ?>
                                    <img src="<?php echo $product->getFirstProductImage()->getThumb(); ?>"
                                         title="<?php echo $product->name ?>" alt="<?php echo $product->name ?>"/>
                                     <?php else: ?>
                                    <img itemprop="image" src="/html/images/kasoko/280x196.png"
                                         alt="<?php echo $product->name; ?>" width="100%">
                                     <?php endif; ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    <div class="product-meta">
                        <div class="name">
                            <a href="/product/item/<?php echo Helpers\Stringify::slugify($product->slug); ?>"><?php echo $product->name ?></a>
                        </div>
                        <div class="price">
                            <span><?php echo Helpers\Stringify::currency_format($product->price); ?></span>
                        </div>
                        <div class="product-description">
                            <p class="description"
                               itemprop="description"><?php echo \Helpers\Stringify::truncate($product->description, 100); ?></p>
                        </div>
                        <div class="cart">
                            <a href="javascript:void(0);" class="btn btn-default"
                               onclick="addToCart('<?php echo $product->getId(); ?>');"><span>Add to Cart</span></a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="pagination-wrap">
            <?php echo $this->paginationHtml; ?>
        </div>
    <?php endif; ?>
</div>