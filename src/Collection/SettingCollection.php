<?php

namespace Settings\Collection;

use FormSchema\Generator\Group as GroupGenerator;
use FormSchema\Schema\Field;
use FormSchema\Schema\Form;
use FormSchema\Schema\Group;
use Illuminate\Support\Collection;
use Settings\Contracts\Setting;

class SettingCollection extends Collection
{

    public static ?\Closure $convertToFormUsing = null;

    public function toKeyValuePair(): SettingCollection
    {
        return $this->mapWithKeys(fn(Setting $setting) => [$setting->key() => settings()->getValue($setting->key())]);
    }

    public function toForm(): Form
    {
        if(static::$convertToFormUsing !== null) {
            return call_user_func(static::$convertToFormUsing, $this->values());
        }

        $form = new Form();

        $this->groupBy(fn(Setting $setting) => collect($setting->getGroups())->first())
            ->map(function(SettingCollection $settingCollection, string $groupId){
                $group = GroupGenerator::make()->getSchema();
                if(\Settings\Setting::store()->groupIsRegistered($groupId)) {
                    if(($groupTitle = \Settings\Setting::store()->getGroupTitle($groupId)) !== null) {
                        $group->setTitle($groupTitle);
                    }
                    if(($groupSubtitle = \Settings\Setting::store()->getGroupSubtitle($groupId)) !== null) {
                        $group->setSubtitle($groupSubtitle);
                    }
                }
                $settingCollection->map(fn(Setting $setting) => $setting->fieldOptions())
                    ->each(fn(Field $field) => $group->addField($field));
                return $group;
            })
            ->each(fn(Group $group) => $form->addGroup($group));

        return $form;
    }

}
