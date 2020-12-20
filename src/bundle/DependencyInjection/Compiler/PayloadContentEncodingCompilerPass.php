<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2020 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace WebPush\Bundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use WebPush\Payload\PayloadExtension;

final class PayloadContentEncodingCompilerPass implements CompilerPassInterface
{
    public const TAG = 'webpush_payload_content_encoding';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(PayloadExtension::class)) {
            return;
        }

        $definition = $container->getDefinition(PayloadExtension::class);
        $taggedServices = $container->findTaggedServiceIds(self::TAG);
        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall('addContentEncoding', [new Reference($id)]);
        }
    }
}