<?php
require_once _PS_MODULE_DIR_ . 's2i_update_img/classes/SlidesLists.php';

class SlideManager
{
    private $module;
    private $context;
    private $id_section;

    public function __construct($module, $id_section)
    {
        $this->module = $module;
        $this->context = Context::getContext();
        $this->id_section = $id_section;
    }

    public function getSlides()
    {
        return Slide::getBySection($this->id_section);
    }

    public function getSlidesWithLang($id_lang = null)
    {
        if ($id_lang === null) {
            $id_lang = $this->context->language->id;
        }

        $slides = $this->getSlides();
        if (!$slides) {
            return [];
        }

        foreach ($slides as &$slide) {
            $slide_lang = SlideLang::getBySlideAndLang($slide['id_slide'], $id_lang);
            $slide = array_merge($slide, $slide_lang ?: []);
        }

        return $slides;
    }

    public function renderSlidesList()
    {
        $slides = $this->getSlidesWithLang();
        return SlidesLists::renderSlidesList($slides);
    }

    public function getSlideById($id_slide)
    {
        $slide = new Slide($id_slide);
        if (!Validate::isLoadedObject($slide) || $slide->id_section != $this->id_section) {
            return false;
        }
        return $slide;
    }
}
