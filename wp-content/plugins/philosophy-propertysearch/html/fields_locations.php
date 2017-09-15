<?php
$LOC = new Locations();
$PS = new PropertySearch();
$PS->ouput_admin_fields($LOC->getFields());