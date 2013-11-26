<?php $this->pageTitle=Yii::app()->name; ?>
<!-- <?php echo $_SERVER['REMOTE_ADDR'];?> -->
<h1>Welcome to <i><?php echo CHtml::encode(Yii::app()->name); ?></i></h1>



<p>These are the administrative functions available to you:</p>
<ol><li><?php echo CHtml::link('Product Edit', $this->createUrl("products/admin")); ?>
            <ul>
            <li>Edit a product and its applications</li>
        </ul>
    <li><?php echo CHtml::link('Application Edit', $this->createUrl("certificationApplications/admin")); ?>
        <ul>
            
            <li>Edit a specific application (deprecated)</li>
        </ul>
    
    <li><?php echo CHtml::link('User View', $this->createUrl("users/admin")); ?></li>
    
    
</ol>

<?php //echo "<pre>\Yii::app()->session = "; print_r(Yii::app()->session); print "</pre>";
?>
<?php //echo "<pre>\$_SESSION = "; print_r($_SESSION); print "</pre>";
?>
