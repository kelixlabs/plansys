<div class="install-pane">
    <div class="install-pane-head">
        <img src="<?= $this->staticUrl("/img/logo.png"); ?>" alt="Logo Plansys" />
    </div>

    <?php if (!isset($_GET['msg'])): ?>
        <table class="table table-bordered table-condensed install-check">
            <?php foreach (Installer::getCheckList() as $group => $item): ?>
                <tr class="<?= Installer::getError($group) ? 'danger' : 'success'; ?>">
                    <td>
                        <a href="#" ><b><?= $group ?></b>
                            <div class="sub <?= Installer::getError($group) ? 'error' : ''; ?>" >
                                <table class="table table-condensed">
                                    <?php foreach ($item as $k => $i): ?>
                                        <tr class="<?= Installer::getError($group, $k) ? 'danger' : 'success'; ?>">
                                            <td><?= $i['title']; ?></td>
                                            <td class="install-status">
                                                <i class="fa <?= Installer::getError($group, $k) ? 'fa-times' : 'fa-check'; ?>"></i>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            </div>
                        </a>
                    </td>
                    <td class="install-status">
                        <i class="fa <?= Installer::getError($group) ? 'fa-times' : 'fa-check'; ?>"></i>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <?php if (empty(Installer::getError())): ?>
            <div class="install-passed"><?= Yii::t("plansys", "All Requirement Passed"); ?></div>

            <a href="#" class="btn btn-success">
                <?= Yii::t("plansys", "Next Step"); ?><i class="fa fa-chevron-right"></i>
            </a>
        <?php else: ?>
            <div class="install-failed">
                <?= Yii::t("plansys", "Please fix error(s) above to continue ..."); ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <br/><br/>
        <div class="alert alert-danger install-error">
            <div class="install-error-head">
                <i class="fa fa-warning"></i> <?= Yii::t("plansys", "Error while initializing plansys"); ?>
            </div>
            <?php
            $p = new CHtmlPurifier();
            $p->options = array('URI.AllowedSchemes' => array(
                    'http' => true,
                    'https' => true,
            ));
            echo $p->purify($_GET['msg']);
            ?>
        </div>
    <?php endif; ?>
</div>