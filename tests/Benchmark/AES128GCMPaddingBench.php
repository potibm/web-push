<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2020-2021 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace WebPush\Tests\Benchmark;

use Nyholm\Psr7\Request;
use PhpBench\Benchmark\Metadata\Annotations\Revs;
use PhpBench\Benchmark\Metadata\Annotations\Subject;
use WebPush\Action;
use WebPush\Message;
use WebPush\Payload\AbstractAESGCM;
use WebPush\Payload\AES128GCM;
use WebPush\Subscription;

/**
 * @BeforeMethods({"init"})
 * @Revs(4096)
 */
class AES128GCMPaddingBench
{
    private AbstractAESGCM $encoder;
    private AbstractAESGCM $encoderWithRecommendedPadding;
    private AbstractAESGCM $encoderWithMaximumPadding;
    private Subscription $subscription;
    private string $message;
    private Request $request;

    public function init(): void
    {
        $this->encoder = AES128GCM::create()->noPadding();
        $this->encoderWithRecommendedPadding = AES128GCM::create()->recommendedPadding();
        $this->encoderWithMaximumPadding = AES128GCM::create()->maxPadding();

        $this->message = Message::create('Hello World!')
            ->withLang('en-GB')
            ->interactionRequired()
            ->withTimestamp(time())
            ->addAction(Action::create('accept', 'Accept'))
            ->addAction(Action::create('cancel', 'Cancel'))
            ->toString()
        ;
        $this->subscription = Subscription::createFromString('{"endpoint":"https://updates.push.services.mozilla.com/wpush/v2/gAAAAABfcsdu1p9BdbYIByt9F76MHcSiuix-ZIiICzAkU9z_p0gnolYLMOi71rqss5pMOZuYJVZLa7rRN58uOgfdsux7k51Ph6KJRFEkf1LqTRMv2d8OhQaL2TR36WUR2d5twzYVwcQJAnTLrhVrWqKVo8ekAonuwyFHDUGzD8oUWpFTK9y2F68","keys":{"auth":"wSfP1pfACMwFesCEfJx4-w","p256dh":"BIlDpD05YLrVPXfANOKOCNSlTvjpb5vdFo-1e0jNcbGlFrP49LyOjYyIIAZIVCDAHEcX-135b859bdsse-PgosU"},"contentEncoding":"aes128gcm"}');
        $this->request = new Request('POST', 'https://www.example.com');
    }

    /**
     * @Subject
     */
    public function encodeWithoutPadding(): void
    {
        $this->encoder->encode($this->message, $this->request, $this->subscription);
    }

    /**
     * @Subject
     */
    public function encodeWithRecommendedPadding(): void
    {
        $this->encoderWithRecommendedPadding->encode($this->message, $this->request, $this->subscription);
    }

    /**
     * @Subject
     */
    public function encodeWithMaximumPadding(): void
    {
        $this->encoderWithMaximumPadding->encode($this->message, $this->request, $this->subscription);
    }
}
