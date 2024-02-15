<?php

declare(strict_types=1);

namespace App\View;
use App\Exceptions\ViewNotFound;

class View
{
    private array $titles = [
        'home-index' => 'Home page',
        'notify-index' => 'Notify page',
        'user-index' => 'User page',
        'file-index' => 'File page',
        '_404-index' => 'Not found page',
        '_400-index' => 'Bad request page',
        '_500-index' => 'Bad gateway page'
    ];
    private string $viewTemplatesPath = 'templates';
    private string $viewLayoutTemplateName = 'layout';
    private string $viewTemplatesExtensions = '.phtml';
    public function __construct(
        protected string $view,
        protected array $params = []
    ){}

    public static function make(string $view, array|object $params = []): static
    {
        return new static($view, $params);
    }
    public function __toString(): string
    {
        return $this->render();
    }

    public function render(): string
    {
        $_thisView = $this->getTemplateName();
        $viewPath = __DIR__ . '/' . $this->viewTemplatesPath . '/'. $_thisView . $this->viewTemplatesExtensions;
        if(!file_exists($viewPath)) {
            throw new ViewNotFound();
        }
        $params = $this->params ?? [];
        ob_start();
        require $viewPath;
        $body = (string) ob_get_clean();
        $title = $this->getTitle();
        $description = $title;
        ob_start();
        require __DIR__ . '/' . $this->viewTemplatesPath . '/'. $this->viewLayoutTemplateName . $this->viewTemplatesExtensions;
        return (string) ob_get_clean();
    }

    private function getTemplateName(): string
    {
        return $this->sanitizeView();
    }
    private function getTitle(): string
    {
        $view = $this->sanitizeView();
        return $this->titles[$view] ?? $this->titles['_500-index'];
    }

    private function sanitizeView(): string
    {
        $view = explode('\\', strtolower($this->view));
        $view = str_replace(
            'controller', '', str_replace('::', '-', array_pop($view)));
        $view = str_replace(
            'service', '', str_replace('::', '-', $view));
            return $view;
    }
}
