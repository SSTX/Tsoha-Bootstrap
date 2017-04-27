<?php

class BaseModel {

    // "protected"-attribuutti on käytössä vain luokan ja sen perivien luokkien sisällä
    protected $validators;

    public function __construct($attributes = null) {
        // Käydään assosiaatiolistan avaimet läpi
        foreach ($attributes as $attribute => $value) {
            // Jos avaimen niminen attribuutti on olemassa...
            if (property_exists($this, $attribute)) {
                // ... lisätään avaimen nimiseen attribuuttin siihen liittyvä arvo
                $this->{$attribute} = $value;
            }
        }
    }

    public function errors() {
        // Lisätään $errors muuttujaan kaikki virheilmoitukset taulukkona
        $errors = array();

        foreach ($this->validators as $validator) {
            // Kutsu validointimetodia tässä ja lisää sen palauttamat virheet errors-taulukkoon
            $errors[] = array_merge($errors, $this->{$validator}());
        }
        return $errors;
    }

    public function prettySubmitTime() {
        if (!empty($this->submitTime)) {
            return date('Y-m-d H:i:s', strtotime($this->submitTime));
        } else if (!empty($this->registerTime)) {
            return date('Y-m-d H:i:s', strtotime($this->registerTime));
        } else {
            return null;
        }
    }

}
