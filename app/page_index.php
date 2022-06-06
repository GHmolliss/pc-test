<h1>Тестовое задание</h1>

<?php if ($this->data['parse']['result'] === 'error') { ?>
    <div class="alert alert-danger" role="alert">
        Код: <?= $this->data['parse']['code'] ?>. <?= $this->data['parse']['message'] ?>
    </div>
<?php } ?>

<form method="get" action="<?= APP_URL ?>">
    <div class="mb-3">
        <label for="url" class="form-label">Ссылка</label>

        <input id="url" name="url" value="<?php if ($this->data['url']) echo $this->data['url']; ?>" type="url" class="form-control" aria-describedby="urlHelp" required>

        <div id="urlHelp" class="form-text">Укажите ссылку на каталог товаров <a href="https://zenden.ru/" target="_blank" title="Zenden">Zenden</a></div>
    </div>

    <div class="mb-3">
        <button type="submit" class="btn btn-primary">Парсинг</button>
    </div>
</form>

<?php if (!empty($this->data['products'])) { ?>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Изображение</th>
                <th scope="col">Наименование</th>
                <th scope="col">Цена</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this->data['products'] as $product) { ?>
                <tr>
                    <th scope="row"><?= $product->id ?></th>
                    <td>
                        <a href="<?= $product->href ?>" target="_blank">
                            <img src="<?= $product->src ?>" class="rounded img-thumbnail w-25" alt="<?= $product->name ?>"></a>
                    </td>
                    <td>
                        <a href="<?= $product->href ?>" target="_blank"><?= $product->name ?></a>
                    </td>
                    <td><?= $product->getPriceFormatted() ?> руб.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } ?>