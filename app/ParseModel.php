<?php

namespace App;

final class ParseModel extends Model
{
    private const TAG_OPEN = '<';
    private const TAG_CLOSE = '>';
    private const ATTRIBUTE_QUOTES = '"';
    private const ATTRIBUTES = [
        'href',
        'data-src',
    ];

    public $params = null;

    private $data = [
        'result' => 'ok',
        'message' => '',
        'code' => 0,
        'search_products' => 0,
        'added_products' => 0,
    ];
    private $url = [];

    public function __construct(ParseParams $parseParams)
    {
        $this->params = $parseParams;
    }

    public function run(?string $url): array
    {
        if ($url === null) {
            return $this->data;
        }

        $Products = new ProductsModel;

        try {
            $this->getURLParams($url);
            $html = $this->getHTML($url);
            $htmlMain = $this->getHTMLMainBlock($html);
            $htmlProducts = $this->getProductsBlocks($htmlMain);
            $products = $this->getProducts($htmlProducts);

            $Products->clear();
            $this->data['added_products'] = $Products->add($products);
        } catch (\Exception $exception) {
            $this->data['result'] = 'error';
            $this->data['message'] = $exception->getMessage();
            $this->data['code'] = $exception->getCode();
        }

        return $this->data;
    }

    private function getURLParams(string $url): void
    {
        if (empty($url)) {
            throw new \Exception('Пожалуйста, укажите адрес', 1000);
        }

        $this->url = parse_url($url);

        if (empty($this->url['scheme']) || empty($this->url['host']) || empty($this->url['path'])) {
            throw new \Exception('Пожалуйста, введите корректный адрес', 1100);
        }

        if ($this->url['host'] !== $this->params->host) {
            throw new \Exception('Пожалуйста, введите корректный адрес', 1101);
        }
    }

    private function getHTML(string $url): string
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);

        $html = curl_exec($ch);

        curl_close($ch);

        if (mb_strpos($html, 'Такой страницы не существует') !== false) {
            throw new \Exception('Такой страницы не существует', 1200);
        }

        return $html;
    }

    private function getHTMLMainBlock($html): string
    {
        $tagStartStrlen = mb_strlen($this->params->mainTagStart);

        $mainPosStart = mb_strpos($html, $this->params->mainTagStart) + $tagStartStrlen;
        $mainPosEnd = mb_strrpos($html, $this->params->mainTagEnd);
        $htmlMain = mb_substr($html, $mainPosStart, $mainPosEnd - $mainPosStart);
        $htmlMain = str_replace(["\r\n", "\n", "\r"], ' ', $htmlMain);

        return $htmlMain;
    }

    private function getProductsBlocks(string $html): array
    {
        if (mb_strpos($html, $this->params->productsBlocks) === false) {
            throw new \Exception('Товары не найдены', 1300);
        }

        $products = explode($this->params->productsBlocks, $html);

        unset($products[0]);

        return $products;
    }

    private function getProducts(array $productsBlocks): array
    {
        $products = [];
        $this->data['search_products'] = count($productsBlocks);

        foreach ($productsBlocks as $html) {


            $productData = $this->getTagInfo($this->params->product, $html);
            $priceData = $this->getTagInfo($this->params->productPrice, $html);
            $imageData = $this->getTagInfo($this->params->productImage, $html, false);

            if (isset($priceData['value'])) {
                $priceData['value'] = $this->preparePrice($priceData['value']);
            }

            if (isset($productData['href'])) {
                $productData['href'] = $this->url['scheme'] . '://' . $this->url['host'] . $productData['href'];
            }

            $Product = new Product();
            $Product->name = $productData['value'] ?? '';
            $Product->href = $productData['href'] ?? '';
            $Product->src = $imageData['data-src'] ?? '';
            $Product->price = $priceData['value'] ?? 0;

            if (empty($Product->name) || empty($Product->href) || empty($Product->src) || empty($Product->price)) continue;

            $products[] = $Product;
        }

        if (empty($products)) {
            throw new \Exception('Товары не найдены', 1400);
        }

        return $products;
    }

    private function preparePrice(string $value): float
    {
        $value = str_replace('&nbsp;', '', $value);
        $value = trim($value, '.-');

        return (float) $value;
    }

    public function getTagInfo(string $needle, string $html, bool $setValue = true)
    {
        $reverse = true;
        $search = true;
        $tag = '';
        $tagInfo = [];
        $cursor = mb_strpos($html, $needle);

        do {
            $symbol = mb_substr($html, $cursor, 1);

            if ($symbol === self::TAG_OPEN) {
                $reverse = false;
            }

            if (!$reverse) {
                $tag .= $symbol;
            }

            $cursor = ($reverse) ? --$cursor : ++$cursor;

            if ($symbol === self::TAG_CLOSE) {
                $search = false;

                $tagInfo = $this->getTagAttribytes($tag);
            }

            if ($tagInfo && $setValue) {
                $tagInfo['value'] = $this->getTagValue($cursor, $html);
            }

            if (!$search) {
                return $tagInfo;
            }
        } while ($search);
    }

    private function getTagValue(int $cursor, string $html): string
    {
        $value = '';

        do {
            $symbol = mb_substr($html, $cursor, 1);

            if ($symbol === self::TAG_OPEN) {
                return $value;
            }

            $value .= $symbol;

            ++$cursor;
        } while (true);
    }

    private function getTagAttribytes(string $html): array
    {
        if (!$html) {
            return [];
        }

        $attribytes['html'] = $html;

        foreach (self::ATTRIBUTES as $attributeName) {
            $cursor = strpos($html, $attributeName);
            $attributeValue = '';

            if ($cursor === false) continue;

            $searchOpenQuotes = true;
            $searchCloseQuotes = true;

            do {
                $symbol = mb_substr($html, $cursor, 1);

                ++$cursor;

                if ($searchOpenQuotes && $symbol !== self::ATTRIBUTE_QUOTES) continue;

                if ($searchOpenQuotes && $symbol === self::ATTRIBUTE_QUOTES) {
                    $searchOpenQuotes = false;

                    continue;
                }

                if ($searchCloseQuotes && $symbol === self::ATTRIBUTE_QUOTES) {
                    $searchCloseQuotes = false;

                    $attribytes[$attributeName] = trim($attributeValue);

                    continue;
                }

                $attributeValue .= $symbol;
            } while ($searchCloseQuotes);
        }

        return $attribytes;
    }
}
