<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_languages
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('stylesheet', 'mod_languages/template.css', array(), true);
?>
<div class="mod-languages<?php echo $moduleclass_sfx ?>">
<?php if ($headerText) : ?>
	<div class="pretext"><p><?php echo $headerText; ?></p></div>
<?php endif; ?>

<?php if ($params->get('dropdown', 1)) : ?>
	<form name="lang" method="post" action="<?php echo htmlspecialchars(JURI::current()); ?>">
	<span class="lang-icon"></span>
	<select class="inputbox" onchange="document.location.replace(this.value);" >
	<?php foreach($list as $language):?>
		<option dir=<?php echo JLanguage::getInstance($language->lang_code)->isRTL() ? '"rtl"' : '"ltr"'?> value="<?php echo $language->link;?>" <?php echo $language->active ? 'selected="selected"' : ''?>>
		<?php echo $language->title_native;?></option>
	<?php endforeach; ?>
	</select>
	</form>
<?php else : ?>
	<ul class="<?php echo $params->get('inline', 1) ? 'lang-inline' : 'lang-block';?>">
	<?php foreach($list as $language):?>
		<?php if ($params->get('show_active', 0) || !$language->active):?>
			<li class="<?php echo $language->active ? 'lang-active' : '';?>" dir="<?php echo JLanguage::getInstance($language->lang_code)->isRTL() ? 'rtl' : 'ltr' ?>">
			<?php if ($language->active) { ?>
				<span>
			<?php } else { ?>
				<a href="<?php echo $language->link;?>">
			<?php } ?>
			<?php if ($params->get('image', 1)):?>
				<?php echo JHtml::_('image', 'mod_languages/'.$language->image.'.gif', $language->title_native, array('title'=>$language->title_native), true);?>
				<?php echo $params->get('full_name', 1) ? $language->title_native : $language->sef;?>
			<?php else : ?>
				<?php echo $params->get('full_name', 1) ? $language->title_native : $language->sef;?>
			<?php endif; ?>
			<?php if ($language->active) { ?>
				</span>
			<?php } else { ?>
				</a>
			<?php } ?>
			</li>
		<?php endif;?>
	<?php endforeach;?>
	</ul>
<?php endif; ?>

<?php if ($footerText) : ?>
	<div class="posttext"><p><?php echo $footerText; ?></p></div>
<?php endif; ?>
</div>
