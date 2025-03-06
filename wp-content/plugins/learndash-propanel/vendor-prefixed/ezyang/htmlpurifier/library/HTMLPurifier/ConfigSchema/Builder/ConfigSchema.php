<?php

/**
 * Converts LearnDash_Reports_HTMLPurifier_ConfigSchema_Interchange to our runtime
 * representation used to perform checks on user configuration.
 *
 * @license LGPL-2.1-or-later
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */
class LearnDash_Reports_HTMLPurifier_ConfigSchema_Builder_ConfigSchema
{

    /**
     * @param LearnDash_Reports_HTMLPurifier_ConfigSchema_Interchange $interchange
     * @return LearnDash_Reports_HTMLPurifier_ConfigSchema
     */
    public function build($interchange)
    {
        $schema = new LearnDash_Reports_HTMLPurifier_ConfigSchema();
        foreach ($interchange->directives as $d) {
            $schema->add(
                $d->id->key,
                $d->default,
                $d->type,
                $d->typeAllowsNull
            );
            if ($d->allowed !== null) {
                $schema->addAllowedValues(
                    $d->id->key,
                    $d->allowed
                );
            }
            foreach ($d->aliases as $alias) {
                $schema->addAlias(
                    $alias->key,
                    $d->id->key
                );
            }
            if ($d->valueAliases !== null) {
                $schema->addValueAliases(
                    $d->id->key,
                    $d->valueAliases
                );
            }
        }
        $schema->postProcess();
        return $schema;
    }
}

// vim: et sw=4 sts=4
