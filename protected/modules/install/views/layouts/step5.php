<h1>Step 5: Complete</h1>
<p class="alert alert-success">Installation finished.</p>
<ul>
    <li>Delete folder <code>/modules/install</code></li>
    <li>Delete file <code>/config/db.php</code> if you want to reinstall later</li>
</ul>
<?= Html::a('Go to site', Yii::$app->homeUrl, ['class' => 'btn btn-primary']) ?>