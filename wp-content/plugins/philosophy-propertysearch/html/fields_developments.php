<?php
$PS = new PropertySearch();
$DEV = new Developments();
$PS->ouput_admin_fields($DEV->getFields());
