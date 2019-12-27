<?php
/**
 * Created by PhpStorm.
 * User: Semenov
 * Date: 27.11.2018
 * Time: 12:39
 */

use yii\helpers\Html;
use yii\helpers\Url;

//$this->params['breadcrumbs'][] = [ 'label' => 'Специальность', 'url' => Url::to( [ 'schedule/speciality' ] ) ];
//$this->params['breadcrumbs'][] = [ 'label' => 'Специалист и время приема' ];
?>

<div class="doctor-step">
    <?php if (isset($sortCode)):
        foreach ($offices as $item) {
            if ($item['sortCode'] == $sortCode) {
                echo '<h2 id="officeName">' . $item['officeName'] . '</h2>';
            }
        };?>
        <?php if (isset($specName)): ?>
            <h2 id="windowName"><?php echo "Выбранная специальность: " . mb_strtoupper($specName); ?></h2>
        <?php endif; ?>
        <?php if (isset($windowName)): ?>
            <h2 id="windowName"><?php echo $windowName; ?></h2>
        <?php endif;?>
        <hr class="primary" style="padding-bottom: 20px">
    <?php endif;?>
    <div class="container-fluid">
		<?php if ( isset( $doctors ) && isset( $specId ) && count( $doctors ) > 0 ): ?>
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1">
                <div class="swiper-container swiper-box" style="height: 600px;">
                    <div class="swiper-wrapper">
        				<?php foreach ( $doctors as $item ): ?>
                            <div class="swiper-slide">
		    					<?= Html::a( $item['TEXT'],
									Url::to( [
										'schedule/calendar',
                                        'specId'  => $specId,
                                        'specName' => $specName,
			        					'docId'   => $item['KEYID'],
					    				'docName' => $item['TEXT'],
                                    ] ), [ 'class' => 'btn btn-block btn-primary btn-kiosk-sm' ] ) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php if ( count( $doctors ) > 5 ): ?>
                <div class="col-sm-1 pagination-container">
                    <button class="btn btn-default btn-pagination button-prev">
                        <i class="fas fa-chevron-circle-up"></i>
                    </button>
                    <button class="btn btn-default btn-pagination button-next">
                        <i class="fas fa-chevron-circle-down"></i>
                    </button>
                </div>
			<?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
