<?php

namespace Core\Classes;


// Bug - 'text' property displays in html attrs 

class HTML
{
	public function tag($data)
	{
		self::getAttrsString($data);
	}

	public static function link($inputData)
	{
	    $linkText = self::pop($inputData['text']);
	    $attrs = self::stringifyAttrs($inputData);

        return '<a' . $attrs . '/>' . $linkText . '</a>';
	}
	
	public static function input($inputData)
	{
		$attrs = self::stringifyAttrs($inputData);
		
		return '<input' . $attrs . '/>';
	}

	public static function textarea($inputData)
    {
        $text = self::pop($inputData['text']);
        $attrs = self::stringifyAttrs($inputData);

        return '<textarea' . $attrs . '>' . $text . '</textarea>';
    }
	
	public static function hidden($inputData)
	{
        $attrs = self::stringifyAttrs($inputData);

        return '<input type="hidden"' . $attrs . '>';
	}

	public static function button($inputData)
	{
		$attrs = self::stringifyAttrs($inputData);

		return '<input type="button"' . $attrs . '/>';
	}
	
	public static function reset($inputData)
	{
		$attrs = self::stringifyAttrs($inputData);
		
		return '<input type="reset"' . $attrs . '/>';
	}
	
	public static function submit($inputData)
	{
		$attrs = self::stringifyAttrs($inputData);
		
		return '<input type="submit"' . $attrs . '/>';
	}

	public static function select($inputData)
    {
        $options = self::pop($inputData['options']);
        unset($inputData['options']);

        $data = self::pop($inputData['data']);
        unset($inputData['data']);
		
		$selected = self::pop($inputData['selected']);
		unset($inputData['selected']);
		
        $attrs = self::stringifyAttrs($inputData);

        $html = '<select' . $attrs . '>';

		if (is_array($data) && count($data))
		{
			foreach ($data as $params)
			{
				if (is_array($selected))
				{
					$selectString = (in_array($params->{$options['value']}, $selected)) ? 'selected' : '';
				}
				else
				{
					$selectString = ($selected == $params->{$options['value']}) ? 'selected' : '';
				}
				
				$html .= '<option value="' . $params->{$options['value']} . '"' . $selectString . '>' . $params->{$options['text']} . '</option>';
			}
		}

        $html .= '</select>';
		
        return $html;
    }

    public static function multiselect($inputData)
    {
        $inputData['multiple'] = 'multiple';

        return self::select($inputData);
    }

	public static function checkbox($inputData, $labelData = [])
	{
        $attrs = self::stringifyAttrs($inputData);
		
        $text = self::pop($labelData['text']) ?? '';
        $labelAttrs = self::stringifyAttrs($labelData);
		
        if (!empty($inputData['id']))
        {
			$html = '<input type="checkbox"' . $attrs . '/>';
            $html .= '<label' . $labelAttrs . ' for="' . $inputData['id'] . '">' . $text . '</label>';
        }
        else
        {
            $html = '<label>' . $text;
			$html .= '<input type="checkbox"' . $attrs . '/>';
            $html .= '</label>';
        }
		
		return $html;
	}
	
    public static function block($type, $inputData, $blockData)
    {
        $blockText = self::pop($blockData['text']);
        $blockAttrs = self::stringifyAttrs($blockData);

        $html = '<div' . $blockAttrs . '>';

        if (!empty($inputData['id']))
        {
            $html .= '<label for="' . $inputData['id'] . '">' . $blockText . '</label>';
            $html .= self::{$type}($inputData);
        }
        else
        {
            $html .= '<label>' . $blockText;
            $html .= self::{$type}($inputData);
            $html .= '</label>';
        }

        $html .= '</div>';

        return $html;
    }
	
	public static function inputBlock($inputData, $blockData)
	{
        return self::block('input', $inputData, $blockData);
	}

	public static function textareaBlock($inputData, $blockData)
	{
        return self::block('textarea', $inputData, $blockData);
	}

	public static function buttonBlock($inputData, $blockData)
    {
        return self::block('button', $inputData, $blockData);
    }

	public static function submitBlock($inputData, $blockData)
    {
        return self::block('submit', $inputData, $blockData);
    }

	public static function selectBlock($inputData, $blockData)
    {
        return self::block('select', $inputData, $blockData);
    }

	public static function multiselectBlock($inputData, $blockData)
    {
        return self::block('multiselect', $inputData, $blockData);
    }
	
	public static function checkboxBlock($inputData, $labelData = [], $blockData = [])
    {
        $blockAttrs = self::stringifyAttrs($blockData);
		
		$html = '<div' . $blockAttrs . '>';
		$html .= self::checkbox($inputData, $labelData);
        $html .= '</div>';

        return $html;
	}

	public static function errors($errors)
    {
        $result = '';

        if (!empty($errors))
		{
			if (is_array($errors))
			{
				$result .= '<div class="p-2 mb-2">';

				foreach ($errors as $key => $value)
				{
					$result .= '<div class="text-danger">' . $value . '</div>';
				}

				$result .= '</div>';
			}
			else if (is_string($errors))
			{
				$result .= '<div class="p-2 mb-2">';
				$result .= '<div class="text-danger">' . $errors . '</div>';
				$result .= '</div>';
			}
		}

        return $result;
    }

    public static function pagination($tabs = 4, $active = 1, $urlStart = '', $urlEnd = '')
    {
        $html = '';

        $html .= '<nav aria-label="...">';
        $html .= '<ul class="pagination">';
        $html .= '<li class="page-item">';
        $html .= '<a class="page-link" href="#" tabindex="-1">Previous</a>';
        $html .= '</li>';

        for ($i = 1; $i <= $tabs; $i++)
        {
            if ($i === $active)
            {
                $html .= '<li class="page-item active">';
                $html .= '<a class="page-link" href="' . $urlStart . '?page=' . $i . $urlEnd . '">' . $i;
                $html .= '<span class="sr-only">(current)</span></a>';
                $html .= '</li>';
            }
            else
            {
                $html .= '<li class="page-item">';
                $html .= '<a class="page-link" href="' . $urlStart . '?page=' . $i . $urlEnd . '">' . $i;
                $html .= '</a>';
                $html .= '</li>';
            }
        }

        $html .= '<li class="page-item">';
        $html .= '<a class="page-link" href="#">Next</a>';
        $html .= '</li>';
        $html .= '</ul>';
        $html .= '</nav>';

        return $html;
    }
	
	public static function stringifyAttrs($data)
	{
		$result = '';
		
		foreach ($data as $key => $value)
		{
			$result .= ' ' . $key . '="' . $value . '"';
		}
		
		return $result;
	}

	private static function pop(&$data)
    {
        $result = null;

		if (!empty($data))
        {
            $result = $data;

            unset($data);
        }

		return $result;
    }
}