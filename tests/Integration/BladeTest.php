<?php

namespace Settings\Tests\Integration;

use Illuminate\View\Compilers\BladeCompiler;
use Settings\Setting;
use Settings\Tests\TestCase;
use Settings\Tests\Traits\CreatesSettings;
use function resolve;

class BladeTest extends TestCase
{
    use CreatesSettings;

    private BladeCompiler $blade;

    public function setUp(): void
    {
        parent::setUp();

        $this->blade = resolve('blade.compiler');
    }

    /** @test */
    public function it_renders_the_right_js(){
        $setting1 = $this->createSetting('setting1', 'My Setting One');
        $setting2 = $this->createSetting('setting2', 'My Setting Two');

        Setting::loadManySettings(['setting1', 'setting2']);

        $this->assertDirectiveOutput(
            '<script>window.ESSettings=window.ESSettings||{};ESSettings.setting1="My Setting One";ESSettings.setting2="My Setting Two";</script>',
            '@settings',
            [],
            'Expected to see the js settings printed to the screen'
        );
    }

    /**
     * Evaluate a Blade expression with the given $variables in scope.
     *
     * @param string $expected   The expected output.
     * @param string $expression The Blade directive, as it would be written in a view.
     * @param array  $variables  Variables to extract() into the scope of the eval() statement.
     * @param string $message    A message to display if the output does not match $expected.
     */
    protected function assertDirectiveOutput(
        string $expected,
        string $expression = '',
        array $variables = [],
        string $message = ''
    ) {
        $compiled = $this->blade->compileString($expression);

        /*
         * Normally using eval() would be a big no-no, but when you're working on a templating
         * engine it's difficult to avoid.
         */
        ob_start();
        extract($variables);
        eval(' ?>' . $compiled . '<?php ');
        $output = ob_get_clean();

        $this->assertEquals($expected, $output, $message);
    }

}
