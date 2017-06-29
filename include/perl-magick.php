<div class="magick-header">
<p class="text-center"><a href="#installation">Installation</a> • <a href="#overview">Overview</a> • <a href="#example">Example Script</a> • <a href="#read">Read or Write an Image</a> • <a href="#manipulate">Manipulate an Image</a> • <a href="#set-attribute">Set an Image Attribute</a> • <a href="#get-attribute">Get an Image Attribute</a> • <a href="#compare">Compare an Image to its Reconstruction</a> • <a href="#montage">Create an Image Montage</a> • <a href="#blobs">Working with Blobs</a> • <a href="#direct-access">Direct-access to Image Pixels</a> • <a href="#miscellaneous">Miscellaneous Methods</a> • <a href="#exceptions">Handling Exceptions</a>• <a href="#constants">Constant</a> </p>

<a id="introduction"></a>
<p class="lead magick-description"><a href="<?php echo $_SESSION['RelativePath']?>/../script/mirror.php">PerlMagick</a> is an objected-oriented <a href="http://www.perl.com/perl/">Perl</a> interface to ImageMagick. Use the module to read, manipulate, or write an image or image sequence from within a Perl script. This makes it very suitable for Web CGI scripts. You must have ImageMagick 6.5.5 or above and Perl version 5.005_02 or greater installed on your system for PerlMagick to build properly.</p>

<p>There are a number of useful scripts available to show you the value of PerlMagick. You can do Web based image manipulation and conversion with <a href="https://www.imagemagick.org/download/perl">MagickStudio</a>, or use <a href="http://git.imagemagick.org/repos/ImageMagick/PerlMagick/demo">L-systems</a> to create images of plants using mathematical constructs, and finally navigate through collections of thumbnail images and select the image to view with the <a href="http://webmagick.sourceforge.net/">WebMagick Image Navigator</a>.</p>

<p>You can try PerlMagick from your Web browser at the <a href="https://www.imagemagick.org/MagickStudio/scripts/MagickStudio.cgi">ImageMagick Studio</a>. Or, you can see <a href="<?php echo $_SESSION['RelativePath']?>/../script/examples.php">examples</a> of select PerlMagick functions.</p>

<h2 class="magick-header"><a id="installation"></a>Installation</h2>

<p><b>UNIX</b></p>

<p>Is PerlMagick available from your system RPM repository?  For example, on our CentOS system, we install PerlMagick thusly:</p>

<pre><code>
yum install ImageMagick-perl
</code></pre>

<p>If not, you must install PerlMagick from the ImageMagick source distribution.  Download the latest <a href="https://www.imagemagick.org/download/ImageMagick.tar.gz">source</a> release.</p>

<p>Unpack the distribution with this command:</p>

<pre><code>
tar xvzf ImageMagick.tar.gz
</code></pre>

<p>Next configure and compile ImageMagick:</p>

<?php crt("cd ImageMagick-" . MagickLibVersionText, "", "./configure -with-perl", "", "make"); ?>

<p>If ImageMagick / PerlMagick configured and compiled without complaint, you are ready to install it on your system.  Administrator privileges are required to install.  To install, type</p>

<pre><code>
sudo make install
</code></pre>

<p>You may need to configure the dynamic linker run-time bindings:</p>

<pre><code>
sudo ldconfig /usr/local/lib
</code></pre>


<p>Finally, verify the PerlMagick install worked properly, type</p>

<pre><code>
perl -MImage::Magick -le 'print Image::Magick->QuantumDepth'
</code></pre>

<p>Congratulations, you have a working ImageMagick distribution and you are ready to use PerlMagick to <a href="https://www.imagemagick.org/Usage/">convert, compose, or edit</a> your images.</p>

<p><b>Windows XP / Windows 2000</b></p>

<p>ImageMagick must already be installed on your system. Also, the ImageMagick source distribution for <a href="<?php echo $_SESSION['RelativePath']?>/../script/mirror.php">Windows 2000</a> is required. You must also have the <code>nmake</code> from the Visual C++ or J++ development environment. Copy <code>\bin\IMagick.dll</code> and <code>\bin\X11.dll</code> to a directory in your dynamic load path such as <code>c:\perl\site\5.00502</code>.</p>

<p>Next, type</p>

<pre><code>
cd PerlMagick
perl Makefile.nt
nmake
nmake install
</code></pre>

<p>See the <a href="http://www.dylanbeattie.net/magick/">PerlMagick Windows HowTo</a> page for further installation instructions.</p>

<p><b>Running the Regression Tests</b></p>

<p>To verify a correct installation, type</p>

<pre><code>
make test
</code></pre>

<p>Use <code>nmake test</code> under Windows. There are a few demonstration scripts available to exercise many of the functions PerlMagick can perform. Type</p>

<pre><code>
cd demo
make
</code></pre>

<p>You are now ready to utilize the PerlMagick methods from within your Perl scripts.</p>

<h2 class="magick-header"><a id="overview"></a>Overview</h2>

<p>Any script that wants to use PerlMagick methods must first define the methods within its namespace and instantiate an image object. Do this with:</p>

<pre><code>
use Image::Magick;

$image = Image::Magick-&gt;new;
</code></pre>

<p>PerlMagick is <var>quantum</var> aware.  You can request a specific quantum depth when you instantiate an image object:</p>

<pre><code>
use Image::Magick::Q16;

$image = Image::Magick::Q16-&gt;new;
</code></pre>

<p>The new() method takes the same parameters as <a href="#set-attribute">SetAttribute</a> . For example,</p>

<pre><code>
$image = Image::Magick-&gt;new(size=&gt;'384x256');
</code></pre>

<p>Next you will want to read an image or image sequence, manipulate it, and then display or write it. The input and output methods for PerlMagick are defined in <a href="#read">Read or Write an Image</a>. See <a href="#set-attribute">Set an Image Attribute</a> for methods that affect the way an image is read or written. Refer to <a href="#manipulate">Manipulate an Image</a> for a list of methods to transform an image. <a href="#get-attribute">Get an Image Attribute</a> describes how to retrieve an attribute for an image. Refer to <a href="#montage">Create an Image Montage</a> for details about tiling your images as thumbnails on a background. Finally, some methods do not neatly fit into any of the categories just mentioned. Review <a href="#misc">Miscellaneous Methods</a> for a list of these methods.</p>

<p>Once you are finished with a PerlMagick object you should consider destroying it. Each image in an image sequence is stored in virtual memory. This can potentially add up to mebibytes of memory. Upon destroying a PerlMagick object, the memory is returned for use by other Perl methods. The recommended way to destroy an object is with <code>undef</code>:</p>

<pre><code>
undef $image;
</code></pre>

<p>To delete all the images but retain the <code>Image::Magick</code> object use</p>

<pre><code>
@$image = ();
</code></pre>

<p>and finally, to delete a single image from a multi-image sequence, use</p>

<pre><code>
undef $image-&gt;[$x];
</code></pre>

<p>The next section illustrates how to use various PerlMagick methods to manipulate an image sequence.</p>

<p>Some of the PerlMagick methods require external programs such as <a href="http://www.cs.wisc.edu/~ghost/">Ghostscript</a>. This may require an explicit path in your PATH environment variable to work properly. For example (in Unix),</p>

<pre><code>
$ENV{PATH}' . "='/../bin:/usr/bin:/usr/local/bin';
</code></pre>

<h2 class="magick-header"><a id="example"></a>Example Script</h2>

<p>Here is an example script to get you started:</p>

<pre><code>
#!/usr/local/bin/perl
use Image::Magick;<br />
my($image, $x);<br />
$image = Image::Magick-&gt;new;
$x = $image-&gt;Read('girl.png', 'logo.png', 'rose.png');
warn "$x" if "$x";<br />
$x = $image-&gt;Crop(geometry=&gt;'100x100+100+100');
warn "$x" if "$x";<br />
$x = $image-&gt;Write('x.png');
warn "$x" if "$x";
</code></pre>

<p>The script reads three images, crops them, and writes a single image as a GIF animation sequence. In many cases you may want to access individual images of a sequence. The next example illustrates how this done:</p>

<pre class="pre-scrollable"><code>#!/usr/local/bin/perl
use Image::Magick;<br />
my($image, $p, $q);<br />
$image = new Image::Magick;
$image-&gt;Read('x1.png');
$image-&gt;Read('j*.jpg');
$image-&gt;Read('k.miff[1, 5, 3]');
$image-&gt;Contrast();
for ($x = 0; $image-&gt;[$x]; $x++)
{
  $image-&gt;[$x]-&gt;Frame('100x200') if $image-&gt;[$x]-&gt;Get('magick') eq 'GIF';
  undef $image-&gt;[$x] if $image-&gt;[$x]-&gt;Get('columns') &lt; 100;
}
$p = $image-&gt;[1];
$p-&gt;Draw(stroke=&gt;'red', primitive=&gt;'rectangle', points=&gt;20,20 100,100');
$q = $p-&gt;Montage();
undef $image;
$q-&gt;Write('x.miff');
</code></pre>

<p>Suppose you want to start out with a 100 by 100 pixel white canvas with a red pixel in the center. Try</p>

<pre><code>
$image = Image::Magick-&gt;new;
$image-&gt;Set(size=&gt;'100x100');
$image-&gt;ReadImage('canvas:white');
$image-&gt;Set('pixel[49,49]'=&gt;'red');
</code></pre>

<p>Here we reduce the intensity of the red component at (1,1) by half:</p>

<pre><code>
@pixels = $image-&gt;GetPixel(x=&gt;1,y=&gt;1);
$pixels[0]*=0.5;
$image-&gt;SetPixel(x=&gt;1,y=&gt;1,color=&gt;\@pixels);
</code></pre>

<p>Or suppose you want to convert your color image to grayscale:</p>

<pre><code>
$image-&gt;Quantize(colorspace=&gt;'gray');
</code></pre>

<p>Let's annotate an image with a Taipai TrueType font:</p>

<pre><code>
$text = 'Works like magick!';
$image-&gt;Annotate(font=&gt;'kai.ttf', pointsize=&gt;40, fill=&gt;'green', text=&gt;$text);
</code></pre>

<p>Perhaps you want to extract all the pixel intensities from an image and write them to STDOUT:</p>

<pre><code>
@pixels = $image-&gt;GetPixels(map=&gt;'I', height=&gt;$height, width=&gt;$width, normalize=&gt;true);
binmode STDOUT;
print pack('B*',join('',@pixels));
</code></pre>

<p>Other clever things you can do with a PerlMagick objects include</p>

<pre><code>
$i = $#$p"+1";   # return the number of images associated with object p
push(@$q, @$p);  # push the images from object p onto object q
@$p = ();        # delete the images but not the object p
$p-&gt;Convolve([1, 2, 1, 2, 4, 2, 1, 2, 1]);   # 3x3 Gaussian kernel
</code></pre>

  <h2 class="magick-header"><a id="read"></a>Read or Write an Image</h2>

<p>Use the methods listed below to either read, write, or display an image or image sequence:</p>

<table class="table table-sm table-striped">
<caption>Read or Write Methods</caption>
<colgroup>
  <col width="20%" />
  <col width="20%" />
  <col width="20%" />
  <col width="40%" />
</colgroup>
<tbody>

  <tr>
    <th>Method</th>
    <th>Parameters</th>
    <th>Return Value</th>
    <th>Description</th>
  </tr>

  <tr>
    <td>Read</td>
    <td>one or more filenames</td>
    <td>the number of images read</td>
    <td>read an image or image sequence</td>
  </tr>

  <tr>
    <td>Write</td>
    <td>filename</td>
    <td>the number of images written</td>
    <td>write an image or image sequence</td>
  </tr>

  <tr>
    <td>Display</td>
    <td>server name</td>
    <td>the number of images displayed</td>
    <td>display the image or image sequence to an X server</td>
  </tr>

  <tr>
    <td>Animate</td>
    <td>server name</td>
    <td>the number of images animated</td>
    <td>animate image sequence to an X server</td>
  </tr>
</tbody>
</table>

<p>For convenience, methods Write(), Display(), and Animate() can take any parameter that <a href="#set-attribute">SetAttribute</a> knows about. For example,</p>

<pre><code>
$image-&gt;Write(filename=&gt;'image.png', compression=&gt;'None');
</code></pre>

<p>Use <code>-</code> as the filename to method Read() to read from standard in or to method Write() to write to standard out:</p>

<pre><code>
binmode STDOUT;
$image-&gt;Write('png:-');
</code></pre>

<p>To read an image in the GIF format from a PERL filehandle, use:</p>

<pre><code>
$image = Image::Magick-&gt;new;
open(IMAGE, 'image.gif');
$image-&gt;Read(file=&gt;\*IMAGE);
close(IMAGE);
</code></pre>

<p>To write an image in the PNG format to a PERL filehandle, use:</p>

<pre><code>
$filename = "image.png";
open(IMAGE, ">$filename");
$image-&gt;Write(file=&gt;\*IMAGE, filename=&gt;$filename);
close(IMAGE);
</code></pre>

<p>Note, reading from or writing to a Perl filehandle may fail under Windows due to different versions of the C-runtime libraries between ImageMagick and the ActiveState Perl distributions or if one of the DLL's is linked with the /MT option.  See <a href="http://msdn.microsoft.com/en-us/library/ms235460.aspx">Potential Errors Passing CRT Objects Across DLL Boundaries</a> for an explanation.</p>

<p>If <code>%0Nd, %0No, or %0Nx</code> appears in the filename, it is interpreted as a printf format specification and the specification is replaced with the specified decimal, octal, or hexadecimal encoding of the scene number. For example,</p>

<pre><code>
image%03d.miff
</code></pre>

<p>converts files image000.miff, image001.miff, etc.</p>

<p>You can optionally add <i>Image</i> to any method name. For example, ReadImage() is an alias for method Read().</p>

<h2 class="magick-header"><a id="manipulate"></a>Manipulate an Image</h2>

<p>Once you create an image with, for example, method ReadImage() you may want to operate on it. Below is a list of all the image manipulations methods available to you with PerlMagick.  There are <a href="<?php echo $_SESSION['RelativePath']?>/../script/examples.php">examples</a> of select PerlMagick methods. Here is an example call to an image manipulation method:</p>

<pre><code>
$image-&gt;Crop(geometry=&gt;'100x100+10+20');
$image-&gt;[$x]-&gt;Frame("100x200");
</code></pre>

<p>And here is a list of other image manipulation methods you can call:</p>

<table class="table table-sm table-striped">
<caption>Image Manipulation Methods</caption>
<tbody>
  <tr>
    <th>Method</th>
    <th style="width: 40%">Parameters</th>
    <th style="width: 40%">Description</th>
  </tr>

  <tr>
    <td>AdaptiveBlur</td>
    <td>geometry=&gt;<i>geometry</i>, radius=&gt;<i>double</i>, sigma=&gt;<i>double</i>, bias=&gt;<i>double</i>, channel=&gt;{All, Default, Alpha, Black, Blue, CMYK, Cyan, Gray, Green, Index, Magenta, Opacity, Red, RGB, Yellow}</td>
    <td>adaptively blur the image with a Gaussian operator of the given radius and standard deviation (sigma).  Decrease the effect near edges.</td>
  </tr>

  <tr>
    <td>AdaptiveResize</td>
    <td>geometry=&gt;<i>geometry</i>, width=&gt;<i>integer</i>, height=&gt;<i>integer</i>, filter=&gt;{Point, Box, Triangle, Hermite, Hanning, Hamming, Blackman, Gaussian, Quadratic, Cubic, Catrom, Mitchell, Lanczos, Bessel, Sinc}, support=&gt;<i>double</i>, blur=&gt;<i>double</i></td>
    <td>adaptively resize image using data dependant triangulation. Specify <code>blur</code> &gt; 1 for blurry or &lt; 1 for sharp</td>
  </tr>

  <tr>
    <td>AdaptiveSharpen</td>
    <td>geometry=&gt;<i>geometry</i>, radius=&gt;<i>double</i>, sigma=&gt;<i>double</i>, bias=&gt;<i>double</i>, channel=&gt;{All, Default, Alpha, Black, Blue, CMYK, Cyan, Gray, Green, Index, Magenta, Opacity, Red, RGB, Yellow}</td>
    <td>adaptively sharpen the image with a Gaussian operator of the given radius and standard deviation (sigma).  Increase the effect near edges.</td>
  </tr>

  <tr>
    <td>AdaptiveThreshold</td>
    <td>geometry=&gt;<i>geometry</i>, width=&gt;<i>integer</i>, height=&gt;<i>integer</i>, offset=&gt;<i>integer</i></td>
    <td>local adaptive thresholding.</td>
  </tr>

  <tr>
    <td>AddNoise</td>
    <td>noise=&gt;{Uniform, Gaussian, Multiplicative, Impulse, Laplacian, Poisson}, attenuate=&gt;<i>double</i>, channel=&gt;{All, Default, Alpha, Black, Blue, CMYK, Cyan, Gray, Green, Index, Magenta, Opacity, Red, RGB, Yellow}</td>
    <td>add noise to an image</td>
  </tr>

  <tr>
    <td>AffineTransform</td>
    <td>affine=&gt;<i>array of float values</i>, translate=&gt;<i>float, float</i>, scale=&gt; <i>float, float</i>, rotate=&gt;<i>float</i>, skewX=&gt;<i>float</i>, skewY=&gt;<i>float</i>, interpolate={Average, Bicubic, Bilinear, Filter, Integer, Mesh, NearestNeighbor}, background=&gt;<i><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></i></td>
    <td>affine transform image</td>
  </tr>

  <tr>
    <td>Affinity</td>
    <td>image=&gt;<i>image-handle</i>, method=&gt;{None, FloydSteinberg, Riemersma}</td>
    <td>choose a particular set of colors from this image</td>
  </tr>

  <tr>
    <td>Annotate</td>
    <td>text=&gt;<i>string</i>, font=&gt;<i>string</i>, family=&gt;<i>string</i>, style=&gt;{Normal, Italic, Oblique, Any}, stretch=&gt;{Normal, UltraCondensed, ExtraCondensed, Condensed, SemiCondensed, SemiExpanded, Expanded, ExtraExpanded, UltraExpanded}, weight=&gt;<i>integer</i>, pointsize=&gt;<i>integer</i>, density=&gt;<i>geometry</i>, stroke=&gt;<i><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></i>, strokewidth=&gt;<i>integer</i>, fill=&gt;<i><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></i>, undercolor=&gt;<i><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></i>, kerning=&gt;<i>float</i>, geometry=&gt;<i>geometry</i>, gravity=&gt;{NorthWest, North, NorthEast, West, Center, East, SouthWest, South, SouthEast}, antialias=&gt;{true, false}, x=&gt;<i>integer</i>, y=&gt;<i>integer</i>, affine=&gt;<i>array of float values</i>, translate=&gt;<i>float, float</i>, scale=&gt;<i>float, float</i>, rotate=&gt;<i>float</i>. skewX=&gt;<i>float</i>, skewY=&gt; <i>float</i>, align=&gt;{Left, Center, Right}, encoding=&gt;{UTF-8}, interline-spacing=&gt;<i>double</i>, interword-spacing=&gt;<i>double</i>, direction=&gt;{right-to-left, left-to-right}</td>
    <td>annotate an image with text. See <a href="#misc">QueryFontMetrics</a> to get font metrics without rendering any text.</td>
  </tr>

  <tr>
    <td>AutoGamma</td>
    <td>channel=&gt;{All, Default, Alpha, Black, Blue, CMYK, Cyan, Gray, Green, Index, Magenta, Opacity, Red, RGB, Yellow}</td>
    <td>automagically adjust gamma level of image</td>
  </tr>

  <tr>
    <td>AutoLevel</td>
    <td>channel=&gt;{All, Default, Alpha, Black, Blue, CMYK, Cyan, Gray, Green, Index, Magenta, Opacity, Red, RGB, Yellow}</td>
    <td>automagically adjust color levels of image</td>
  </tr>

  <tr>
    <td>AutoOrient</td>
    <td><br /></td>
    <td>adjusts an image so that its orientation is suitable for viewing (i.e. top-left orientation)</td>
  </tr>

  <tr>
    <td>BlackThreshold</td>
    <td>threshold=&gt;<i>string</i>, channel=&gt;{All, Default, Alpha, Black, Blue, CMYK, Cyan, Gray, Green, Index, Magenta, Opacity, Red, RGB, Yellow}</td>
    <td>force all pixels below the threshold intensity into black</td>
  </tr>

  <tr>
    <td>BlueShift</td>
    <td>factor=&gt;<i>double</i>,</td>
    <td>simulate a scene at nighttime in the moonlight.  Start with a factor of 1.5.</td>
  </tr>

  <tr>
    <td>Blur</td>
    <td>geometry=&gt;<i>geometry</i>, radius=&gt;<i>double</i>, sigma=&gt;<i>double</i>, bias=&gt;<i>double</i>, channel=&gt;{All, Default, Alpha, Black, Blue, CMYK, Cyan, Gray, Green, Index, Magenta, Opacity, Red, RGB, Yellow}</td>
    <td>reduce image noise and reduce detail levels with a Gaussian operator of the given radius and standard deviation (sigma).</td>
  </tr>

  <tr>
    <td>Border</td>
    <td>geometry=&gt;<i>geometry</i>, width=&gt;<i>integer</i>, height=&gt;<i>integer</i>, bordercolor=&gt;<i><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></i>,  compose=&gt;{Undefined, Add, Atop, Blend, Bumpmap, Clear, ColorBurn, ColorDodge, Colorize, CopyBlack, CopyBlue, CopyCMYK, Cyan, CopyGreen, Copy, CopyMagenta, CopyOpacity, CopyRed, RGB, CopyYellow, Darken, Dst, Difference, Displace, Dissolve, DstAtop, DstIn, DstOut, DstOver, Dst, Exclusion, HardLight, Hue, In, Lighten, Luminize, Minus, Modulate, Multiply, None, Out, Overlay, Over, Plus, ReplaceCompositeOp, Saturate, Screen, SoftLight, Src, SrcAtop, SrcIn, SrcOut, SrcOver, Src, Subtract, Threshold, Xor },</td>
    <td>surround the image with a border of color</td>
  </tr>

  <tr>
    <td>CannyEdge</td>
    <td>geometry=&gt;<i>geometry</i>, radius=&gt;<i>double</i>, sigma=&gt;<i>double</i>, 'lower-percent'=&gt;<i>double</i>, 'upper-percent'=&gt;<i>double</i></td>
    <td>use a multi-stage algorithm to detect a wide range of edges in the image (e.g. CannyEdge('0x1+10%+40%')).</td>
  </tr>

  <tr>
    <td>Charcoal</td>
    <td>geometry=&gt;<i>geometry</i>, radius=&gt;<i>double</i>, sigma=&gt;<i>double</i></td>
    <td>simulate a charcoal drawing</td>
  </tr>

  <tr>
    <td>Chop</td>
    <td>geometry=&gt;<i>geometry</i>, width=&gt;<i>integer</i>, height=&gt;<i>integer</i>, x=&gt;<i>integer</i>, y=&gt;<i>integer</i>, gravity=&gt;{NorthWest, North, NorthEast, West, Center, East, SouthWest, South, SouthEast}</td>
    <td>chop an image</td>
  </tr>

  <tr>
    <td>Clamp</td>
    <td>channel=&gt;{Red, RGB, All, etc.}</td>
    <td>set each pixel whose value is below zero to zero and any the pixel whose value is above the quantum range to the quantum range (e.g. 65535) otherwise the pixel value remains unchanged.</td>
  </tr>

  <tr>
    <td>Clip</td>
    <td>id=&gt;<i>name</i>, inside=&gt;<i>{true, false}</i>,</td>
    <td>apply along a named path from the 8BIM profile.</td>
  </tr>

  <tr>
    <td>ClipMask</td>
    <td>mask=&gt;<i>image-handle</i></td>
    <td>clip image as defined by the image mask</td>
  </tr>

  <tr>
    <td>Clut</td>
    <td>image=&gt;<i>image-handle</i>,  interpolate={Average, Bicubic, Bilinear, Filter, Integer, Mesh, NearestNeighbor}, channel=&gt;{Red, RGB, All, etc.}</td>
    <td>apply a color lookup table to an image sequence</td>
  </tr>

  <tr>
    <td>Coalesce</td>
    <td><br /></td>
    <td>merge a sequence of images</td>
  </tr>

  <tr>
    <td>Color</td>
    <td>color=&gt;<i><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></i></td>
    <td>set the entire image to this color.</td>
  </tr>

  <tr>
    <td>ColorDecisionList</td>
    <td>filename=&gt;<i>string</i>,</td>
    <td>color correct with a color decision list.</td>
  </tr>

  <tr>
    <td>Colorize</td>
    <td>fill=&gt;<i><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></i>, blend=&gt;<i>string</i></td>
    <td>colorize the image with the fill color</td>
  </tr>

  <tr>
    <td>ColorMatrix</td>
    <td>matrix=&gt;<i>array of float values</i></td>
    <td>apply color correction to the image.  Although you can use variable sized matrices, typically you use a 5 x 5 for an RGBA image and a 6x6 for CMYKA.  A 6x6 matrix is required for offsets (populate the last column with normalized values).</td>
  </tr>

  <tr>
    <td>Colorspace</td>
    <td>colorspace=&gt;{RGB, Gray, Transparent, OHTA, XYZ, YCbCr, YCC, YIQ, YPbPr, YUV, CMYK}</td>
    <td> set the image colorspace</td>
  </tr>

  <tr>
    <td>Comment</td>
    <td>string</td>
    <td>add a comment to your image</td>
  </tr>

  <tr>
    <td>CompareLayers</td>
    <td>method=&gt;{any, clear, overlay}</td>
    <td>compares each image with the next in a sequence and returns the minimum bounding region of any pixel differences it discovers.  Images do not have to be the same size, though it is best that all the images are coalesced (images are all the same size, on a flattened canvas, so as to represent exactly how a specific frame should look).</td>
  </tr>

  <tr>
    <td>Composite</td>
    <td>image=&gt;<i>image-handle</i>, compose=&gt;{Undefined, Add, Atop, Blend, Bumpmap, Clear, ColorBurn, ColorDodge, Colorize, CopyBlack, CopyBlue, CopyCMYK, Cyan, CopyGreen, Copy, CopyMagenta, CopyOpacity, CopyRed, RGB, CopyYellow, Darken, Dst, Difference, Displace, Dissolve, DstAtop, DstIn, DstOut, DstOver, Dst, Exclusion, HardLight, Hue, In, Lighten, Luminize, Minus, Modulate, Multiply, None, Out, Overlay, Over, Plus, ReplaceCompositeOp, Saturate, Screen, SoftLight, Src, SrcAtop, SrcIn, SrcOut, SrcOver, Src, Subtract, Threshold, Xor }, mask=&gt;<i>image-handle</i>, geometry=&gt;<i>geometry</i>, x=&gt;<i>integer</i>, y=&gt;<i>integer</i>, gravity=&gt;{NorthWest, North, NorthEast, West, Center, East, SouthWest, South, SouthEast}, opacity=&gt;<i>integer</i>, tile=&gt;{True, False}, rotate=&gt;<i>double</i>, color=&gt;<i><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></i>, blend=&gt;<i>geometry</i>, interpolate=&gt;{undefined, average, bicubic, bilinear, filter, integer, mesh, nearest-neighbor, spline}</td>
    <td>composite one image onto another.  Use the rotate parameter in concert with the tile parameter.</td>
  </tr>

  <tr>
    <td>ConnectedComponents</td>
    <td>connectivity=&gt;<i>integer</i>,</td>
    <td>connected-components uniquely labeled, choose from 4 or 8 way connectivity.</td>
  </tr>

  <tr>
    <td>Contrast</td>
    <td>sharpen=&gt;{True, False}</td>
    <td>enhance or reduce the image contrast</td>
  </tr>

  <tr>
    <td>ContrastStretch</td>
    <td>levels=&gt;<i>string</i>, 'black-point'=&gt;<i>double</i>, 'white-point'=&gt;<i>double</i>, channel=&gt;{Red, RGB, All, etc.}</td>
    <td>improve the contrast in an image by `stretching' the range of intensity values</td>
  </tr>

  <tr>
    <td>Convolve</td>
    <td>coefficients=&gt;<i>array of float values</i>, channel=&gt;{All, Default, Alpha, Black, Blue, CMYK, Cyan, Gray, Green, Index, Magenta, Opacity, Red, RGB, Yellow}, bias=&gt;<i>double</i></td>
    <td>apply a convolution kernel to the image. Given a kernel <i>order</i> , you would supply <i>order*order</i> float values (e.g. 3x3 implies 9 values).</td>
  </tr>

  <tr>
    <td>CopyPixels</td>
    <td>image=&gt;<i>image-handle</i>, geometry=&gt;<i>geometry</i>, width=&gt;<i>integer</i>, height=&gt;<i>integer</i>, x=&gt;<i>integer</i>, y=&gt;<i>integer</i>, offset=&gt;<i>geometry</i>, gravity=&gt;{NorthWest, North, NorthEast, West, Center, East, SouthWest, South, SouthEast}, dx=&gt;<i>integer</i>, dy=&gt;<i>integer</i></td>
    <td>copy pixels from the image as defined by the <code>width</code>x<code>height</code>+<code>x</code>+<code>y</code> to image at offset +<code>dx</code>,+<code>dy</code>.</td>
  </tr>

  <tr>
    <td>Crop</td>
    <td>geometry=&gt;<i>geometry</i>, width=&gt;<i>integer</i>, height=&gt;<i>integer</i>, x=&gt;<i>integer</i>, y=&gt;<i>integer</i>, fuzz=&gt;<i>double</i>, gravity=&gt;{NorthWest, North, NorthEast, West, Center, East, SouthWest, South, SouthEast}</td>
    <td>crop an image</td>
  </tr>

  <tr>
    <td>CycleColormap</td>
    <td>amount=&gt;<i>integer</i></td>
    <td>displace image colormap by amount</td>
  </tr>

  <tr>
    <td>Decipher</td>
    <td>passphrase=&gt;<i>string</i></td>
    <td>convert cipher pixels to plain pixels</td>
  </tr>

  <tr>
    <td>Deconstruct</td>
    <td><br /></td>
    <td>break down an image sequence into constituent parts</td>
  </tr>

  <tr>
    <td>Deskew</td>
    <td>geometry=&gt;<i>string</i>,threshold=&gt;<i>double</i></td>
    <td>straighten the image</td>
  </tr>

  <tr>
    <td>Despeckle</td>
    <td> </td>
    <td>reduce the speckles within an image</td>
  </tr>

  <tr>
    <td>Difference</td>
    <td>image=&gt;<i>image-handle</i></td>
    <td>compute the difference metrics between two images </td>
  </tr>

  <tr>
    <td>Distort</td>
    <td>points=&gt;<i>array of float values</i>, method=&gt;{Affine, AffineProjection, ScaleRotateTranslate, SRT, Perspective, PerspectiveProjection, BilinearForward, BilinearReverse, Polynomial, Arc, Polar, DePolar, Barrel, BarrelInverse, Shepards, Resize}, 'virtual-pixel'=&gt;{Background Black Constant Dither Edge Gray Mirror Random Tile Transparent White}, 'best-fit'=&gt;{True, False}</td>
    <td>distort image</td>
  </tr>

  <tr>
    <td>Draw</td>
    <td>primitive=&gt;{point, line, rectangle, arc, ellipse, circle, path, polyline, polygon, bezier, color, matte, text, @<i>filename</i>}, points=&gt;<i>string</i> , method=&gt;<i>{Point, Replace, Floodfill, FillToBorder, Reset}</i>, stroke=&gt;<i><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></i>, fill=&gt;<i><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></i>, font=&gt;<i>string</i>, pointsize=&gt;<i>integer</i>, strokewidth=&gt;<i>float</i>, antialias=&gt;{true, false}, bordercolor=&gt;<i><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></i>, x=&gt;<i>float</i>, y=&gt;<i>float</i>, dash-offset=&gt;<i>float</i>, dash-pattern=&gt;<i>array of float values</i>, affine=&gt;<i>array of float values</i>, translate=&gt;<i>float, float</i>, scale=&gt;<i>float, float</i>, rotate=&gt;<i>float</i>,  skewX=&gt;<i>float</i>, skewY=&gt;<i>float</i>, interpolate=&gt;{undefined, average, bicubic, bilinear, mesh, nearest-neighbor, spline}, kerning=&gt;<i>float</i>, text=&gt;<i>string</i>, vector-graphics=&gt;<i>string</i>, interline-spacing=&gt;<i>double</i>, interword-spacing=&gt;<i>double</i>, direction=&gt;{right-to-left, left-to-right}</td>
    <td>annotate an image with one or more graphic primitives.</td>
  </tr>

  <tr>
    <td>Encipher</td>
    <td>passphrase=&gt;<i>string</i></td>
    <td>convert plain pixels to cipher pixels</td>
  </tr>

  <tr>
    <td>Edge</td>
    <td>radius=&gt;<i>double</i></td>
    <td>enhance edges within the image with a convolution filter of the given radius.</td>
  </tr>

  <tr>
    <td>Emboss</td>
    <td>geometry=&gt;<i>geometry</i>, radius=&gt;<i>double</i>, sigma=&gt;<i>double</i></td>
    <td>emboss the image with a convolution filter of the given radius and standard deviation (sigma).</td>
  </tr>

  <tr>
    <td>Enhance</td>
    <td><br /></td>
    <td>apply a digital filter to enhance a noisy image</td>
  </tr>

  <tr>
    <td>Equalize</td>
    <td>channel=&gt;{All, Default, Alpha, Black, Blue, CMYK, Cyan, Gray, Green, Index, Magenta, Opacity, Red, RGB, Yellow}<br /></td>
    <td>perform histogram equalization to the image</td>
  </tr>

  <tr>
    <td>Extent</td>
    <td>geometry=&gt;<i>geometry</i>, width=&gt;<i>integer</i>, height=&gt;<i>integer</i>, x=&gt;<i>integer</i>, y=&gt;<i>integer</i>, fuzz=&gt;<i>double</i>, background=&gt;<i><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></i>, gravity=&gt;{NorthWest, North, NorthEast, West, Center, East, SouthWest, South, SouthEast}</td>
    <td>set the image size</td>
  </tr>

  <tr>
    <td>Evaluate</td>
    <td>value=&gt;<i>double</i>, operator=&gt;<i>{Add, And, Divide, LeftShift, Max, Min, Multiply, Or, Rightshift, RMS, Subtract, Xor}</i>, channel=&gt;{All, Default, Alpha, Black, Blue, CMYK, Cyan, Gray, Green, Index, Magenta, Opacity, Red, RGB, Yellow} </td>
    <td>apply an arithmetic, relational, or logical expression to the image</td>
  </tr>

  <tr>
    <td>Filter</td>
    <td>kernel=&gt;<i>string</i>, channel=&gt;{All, Default, Alpha, Black, Blue, CMYK, Cyan, Gray, Green, Index, Magenta, Opacity, Red, RGB, Yellow}, bias=&gt;<i>double</i></td>
    <td>apply a convolution kernel to the image.</td>
  </tr>

  <tr>
    <td>Flip</td>
    <td><br /></td>
    <td>reflect the image scanlines in the vertical direction</td>
  </tr>

  <tr>
    <td>Flop</td>
    <td><br /></td>
    <td>reflect the image scanlines in the horizontal direction</td>
  </tr>

  <tr>
    <td>FloodfillPaint</td>
    <td>geometry=&gt;<i>geometry</i>, channel=&gt;{All, Default, Alpha, Black, Blue, CMYK, Cyan, Gray, Green, Index, Magenta, Opacity, Red, RGB, Yellow}, x=&gt;<i>integer</i>, y=&gt;<i>integer</i> , fill=&gt;<i><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></i>, bordercolor=&gt;<i><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></i>, fuzz=&gt;<i>double</i>, invert=&gt;{True, False}</td>
    <td>changes the color value of any pixel that matches the color of the target pixel and is a neighbor. If you specify a border color, the color value is changed for any neighbor pixel that is not that color.</td>
  </tr>

  <tr>
    <td>ForwardFourierTransform</td>
    <td>magnitude=&gt;{True, False}</td>
    <td>implements the forward discrete Fourier transform (DFT)</td>
  </tr>

  <tr>
    <td>Frame</td>
    <td>geometry=&gt;<i>geometry</i>, width=&gt;<i>integer</i>, height=&gt;<i>integer</i>, inner=&gt;<i>integer</i>, outer=&gt;<i>integer</i>, fill=&gt;<i><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></i>,  compose=&gt;{Undefined, Add, Atop, Blend, Bumpmap, Clear, ColorBurn, ColorDodge, Colorize, CopyBlack, CopyBlue, CopyCMYK, Cyan, CopyGreen, Copy, CopyMagenta, CopyOpacity, CopyRed, RGB, CopyYellow, Darken, Dst, Difference, Displace, Dissolve, DstAtop, DstIn, DstOut, DstOver, Dst, Exclusion, HardLight, Hue, In, Lighten, Luminize, Minus, Modulate, Multiply, None, Out, Overlay, Over, Plus, ReplaceCompositeOp, Saturate, Screen, SoftLight, Src, SrcAtop, SrcIn, SrcOut, SrcOver, Src, Subtract, Threshold, Xor },</td>
    <td>surround the image with an ornamental border</td>
  </tr>

  <tr>
    <td>Function</td>
    <td>parameters=&gt;<i>array of float values</i>, function=&gt;{Sin}, 'virtual-pixel'=&gt;{Background Black Constant Dither Edge Gray Mirror Random Tile Transparent White}</td>
    <td>apply a function to the image</td>
  </tr>

  <tr>
    <td>Gamma</td>
    <td>gamma=&gt;<i>string</i>, channel=&gt;{All, Default, Alpha, Black, Blue, CMYK, Cyan, Gray, Green, Index, Magenta, Opacity, Red, RGB, Yellow}</td>
    <td>gamma correct the image</td>
  </tr>

  <tr>
    <td>GaussianBlur</td>
    <td>geometry=&gt;<i>geometry</i>, radius=&gt;<i>double</i>, sigma=&gt;<i>double</i>, bias=&gt;<i>double</i>, channel=&gt;{All, Default, Alpha, Black, Blue, CMYK, Cyan, Gray, Green, Index, Magenta, Opacity, Red, RGB, Yellow}</td>
    <td>reduce image noise and reduce detail levels with a Gaussian operator of the given radius and standard deviation (sigma).</td>
  </tr>

  <tr>
    <td>GetPixel</td>
    <td>geometry=&gt;<i>geometry</i>, channel=&gt;{All, Default, Alpha, Black, Blue, CMYK, Cyan, Gray, Green, Index, Magenta, Opacity, Red, RGB, Yellow}, normalize=&gt;{true, false}, x=&gt;<i>integer</i>, y=&gt;<i>integer</i></td>
    <td>get a single pixel. By default normalized pixel values are returned.</td>
  </tr>

  <tr>
    <td>GetPixels</td>
    <td>geometry=&gt;<i>geometry</i>, width=&gt;<i>integer</i>, height=&gt;<i>integer</i>, x=&gt;<i>integer</i>, y=&gt;<i>integer</i>, map=&gt;<i>string</i>, normalize=&gt;{true, false}</td>
    <td>get image pixels as defined by the map (e.g. "RGB", "RGBA", etc.).  By default non-normalized pixel values are returned.</td>
  </tr>

  <tr>
    <td>Grayscale</td>
    <td>channel=&gt;{Average, Brightness, Lightness, Rec601Luma, Rec601Luminance, Rec709Luma, Rec709Luminance, RMS}</td>
    <td>convert image to grayscale</td>
  </tr>

  <tr>
    <td>HaldClut</td>
    <td>image=&gt;<i>image-handle</i>,  channel=&gt;{Red, RGB, All, etc.}</td>
    <td>apply a Hald color lookup table to an image sequence</td>
  </tr>

  <tr>
    <td>HoughLine</td>
    <td>geometry=&gt;<i>geometry</i>, width=&gt;<i>double</i>, height=&gt;<i>double</i>, threshold=&gt;<i>double</i></td>
    <td>identify lines in the image (e.g. HoughLine('9x9+195')).</td>
  </tr>

  <tr>
    <td>Identify</td>
    <td>file=&gt;<i>file</i>, features=&gt;<i>distance</i>, unique=&gt;{True, False}</td>
    <td>identify the attributes of an image</td>
  </tr>

  <tr>
    <td>Implode</td>
    <td>amount=&gt;<i>double</i>, interpolate=&gt;{undefined, average, bicubic, bilinear, mesh, nearest-neighbor, spline}</td>
    <td>implode image pixels about the center</td>
  </tr>

  <tr>
    <td>InverseDiscreteFourierTransform</td>
    <td>magnitude=&gt;{True, False}</td>
    <td>implements the inverse discrete Fourier transform (DFT)</td>
  </tr>

  <tr>
    <td>Kuwahara</td>
    <td>geometry=&gt;<i>geometry</i>, radius=&gt;<i>double</i>, sigma=&gt;<i>double</i>, bias=&gt;<i>double</i>, channel=&gt;{All, Default, Alpha, Black, Blue, CMYK, Cyan, Gray, Green, Index, Magenta, Opacity, Red, RGB, Yellow}</td>
    <td>edge preserving noise reduction filter</td>
  </tr>

  <tr>
    <td>Label</td>
    <td>string</td>
    <td>assign a label to an image</td>
  </tr>

  <tr>
    <td>Layers</td>
    <td>method=&gt;{coalesce, compare-any, compare-clear, compare-over, composite, dispose, flatten, merge, mosaic, optimize, optimize-image, optimize-plus, optimize-trans, remove-dups, remove-zero},  compose=&gt;{Undefined, Add, Atop, Blend, Bumpmap, Clear, ColorBurn, ColorDodge, Colorize, CopyBlack, CopyBlue, CopyCMYK, Cyan, CopyGreen, Copy, CopyMagenta, CopyOpacity, CopyRed, RGB, CopyYellow, Darken, Dst, Difference, Displace, Dissolve, DstAtop, DstIn, DstOut, DstOver, Dst, Exclusion, HardLight, Hue, In, Lighten, LinearLight, Luminize, Minus, Modulate, Multiply, None, Out, Overlay, Over, Plus, ReplaceCompositeOp, Saturate, Screen, SoftLight, Src, SrcAtop, SrcIn, SrcOut, SrcOver, Src, Subtract, Threshold, Xor }, dither=&gt;{true, false}</td>
    <td>compare each image the GIF disposed forms of the previous image in the sequence.  From this, attempt to select the smallest cropped image to replace each frame, while preserving the results of the animation.</td>
  </tr>

  <tr>
    <td>Level</td>
    <td>levels=&gt;<i>string</i>, 'black-point'=&gt;<i>double</i>, 'gamma'=&gt;<i>double</i>, 'white-point'=&gt;<i>double</i>, channel=&gt;{Red, RGB, All, etc.}</td>
    <td>adjust the level of image contrast</td>
  </tr>

  <tr>
    <td>LevelColors</td>
    <td>invert=&gt;&gt;{True, False}, 'black-point'=&gt;<i>string</i>,  'white-point'=&gt;<i>string</i>, channel=&gt;{Red, RGB, All, etc.}</td>
    <td>level image with the given colors</td>
  </tr>

  <tr>
    <td>LinearStretch</td>
    <td>levels=&gt;<i>string</i>, 'black-point'=&gt;<i>double</i>, 'white-point'=&gt;<i>double</i></td>
    <td>linear with saturation stretch</td>
  </tr>

  <tr>
    <td>LiquidResize</td>
    <td>geometry=&gt;<i>geometry</i>, width=&gt;<i>integer</i>, height=&gt;<i>integer</i>, delta-x=&gt;<i>double</i>, rigidity=&gt;<i>double</i></td>
    <td>rescale image with seam-carving.</td>
  </tr>

  <tr>
    <td>Magnify</td>
    <td><br /></td>
    <td>double the size of the image with pixel art scaling</td>
  </tr>

  <tr>
    <td>Mask</td>
    <td>mask=&gt;<i>image-handle</i></td>
    <td>composite image pixels as defined by the mask</td>
  </tr>

  <tr>
    <td>MatteFloodfill</td>
    <td>geometry=&gt;<i>geometry</i>, x=&gt;<i>integer</i>, y=&gt;<i>integer</i> , matte=&gt;<i>integer</i>, bordercolor=&gt;<i><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></i>, fuzz=&gt;<i>double</i>, invert=&gt;{True, False}</td>
    <td>changes the matte value of any pixel that matches the color of the target pixel and is a neighbor. If you specify a border color, the matte value is changed for any neighbor pixel that is not that color.</td>
  </tr>

  <tr>
    <td>MeanShift</td>
    <td>geometry=&gt;<i>geometry</i>, width=&gt;<i>double</i>, height=&gt;<i>double</i>, distance=&gt;<i>double</i></td>
    <td>delineate arbitrarily shaped clusters in the image (e.g. MeanShift('7x7+10%')).</td>
  </tr>

  <tr>
    <td>MedianFilter</td>
    <td>geometry=&gt;<i>geometry</i>, width=&gt;<i>integer</i>, height=&gt;<i>integer</i>, channel=&gt;{All, Default, Alpha, Black, Blue, CMYK, Cyan, Gray, Green, Index, Magenta, Opacity, Red, RGB, Yellow}</td>
    <td>replace each pixel with the median intensity pixel of a neighborhood.</td>
  </tr>

  <tr>
    <td>Minify</td>
    <td><br /></td>
    <td>half the size of an image</td>
  </tr>

  <tr>
    <td>Mode</td>
    <td>geometry=&gt;<i>geometry</i>, width=&gt;<i>integer</i>, height=&gt;<i>integer</i>, channel=&gt;{All, Default, Alpha, Black, Blue, CMYK, Cyan, Gray, Green, Index, Magenta, Opacity, Red, RGB, Yellow}</td>
    <td>make each pixel the <var>predominant color</var> of the neighborhood.</td>
  </tr>

  <tr>
    <td>Modulate</td>
    <td>factor=&gt;<i>geometry</i>, brightness=&gt;<i>double</i>, saturation=&gt;<i>double</i>, hue=&gt;<i>double</i>, lightness=&gt;<i>double</i>, whiteness=&gt;<i>double</i>, blackness=&gt;<i>double</i> </td>
    <td>vary the brightness, saturation, and hue of an image by the specified percentage</td>
  </tr>

  <tr>
    <td>Morphology</td>
    <td>kernel=&gt;<i>string</i>, channel=&gt;{All, Default, Alpha, Black, Blue, CMYK, Cyan, Gray, Green, Index, Magenta, Opacity, Red, RGB, Yellow}, iterations=&gt;<i>integer</i></td>
    <td>apply a morphology method to the image.</td>
  </tr>

  <tr>
    <td>MotionBlur</td>
    <td>geometry=&gt;<i>geometry</i>, radius=&gt;<i>double</i>, sigma=&gt;<i>double</i>, angle=&gt;<i>double</i>, bias=&gt;<i>double</i>, channel=&gt;{All, Default, Alpha, Black, Blue, CMYK, Cyan, Gray, Green, Index, Magenta, Opacity, Red, RGB, Yellow}</td>
    <td>reduce image noise and reduce detail levels with a Gaussian operator of the given radius and standard deviation (sigma) at the given angle to simulate the effect of motion</td>
  </tr>

  <tr>
    <td>Negate</td>
    <td>gray=&gt;{True, False}, channel=&gt;{All, Default, Alpha, Black, Blue, CMYK, Cyan, Gray, Green, Index, Magenta, Opacity, Red, RGB, Yellow}</td>
    <td>replace each pixel with its complementary color (white becomes black, yellow becomes blue, etc.)</td>
  </tr>

  <tr>
    <td>Normalize</td>
    <td>channel=&gt;{All, Default, Alpha, Black, Blue, CMYK, Cyan, Gray, Green, Index, Magenta, Opacity, Red, RGB, Yellow}<br /></td>
    <td>transform image to span the full range of color values</td>
  </tr>

  <tr>
    <td>OilPaint</td>
    <td>radius=&gt;<i>integer</i></td>
    <td>simulate an oil painting</td>
  </tr>

  <tr>
    <td>Opaque</td>
    <td>color=&gt;<i><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></i>,
fill=&gt;<i><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></i>, channel=&gt;{All, Default, Alpha, Black, Blue, CMYK, Cyan, Gray, Green, Index, Magenta, Opacity, Red, RGB, Yellow}, invert=&gt;{True, False}</td>
    <td>change this color to the fill color within the image</td>
  </tr>

  <tr>
    <td>OrderedDither</td>
    <td>threshold=&gt;{threshold, checks, o2x2, o3x3, o4x4, o8x8, h4x4a, h6x6a, h8x8a, h4x4o, h6x6o, h8x8o, h16x16o, hlines6x4}, channel=&gt;{All, Default, Alpha, Black, Blue, CMYK, Cyan, Gray, Green, Index, Magenta, Opacity, Red, RGB, Yellow}</td>
    <td>order dither image</td>
  </tr>

  <tr>
    <td>Perceptible</td>
    <td>epsilon=&gt;<i>double</i>, channel=&gt;{Red, RGB, All, etc.}</td>
    <td>set each pixel whose value is less than |<var>epsilon</var>| to <var>-epsilon</var> or <var>epsilon</var> (whichever is closer) otherwise the pixel value remains unchanged..</td>
  </tr>

  <tr>
    <td>Polaroid</td>
    <td>caption=&gt;<i>string</i>, angle=&gt;<i>double</i>, pointsize=&gt;<i>double</i>, font=&gt;<i>string</i>, stroke=&gt; <i><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></i>, strokewidth=&gt;<i>integer</i>, fill=&gt;<i><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></i>, gravity=&gt;{NorthWest, North, NorthEast, West, Center, East, SouthWest, South, SouthEast},  background=&gt;<i><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></i></td>
    <td>simulate a Polaroid picture.</td>
  </tr>

  <tr>
    <td>Posterize</td>
    <td>levels=&gt;<i>integer</i>, dither=&gt;{True, False}</td>
    <td>reduce the image to a limited number of color level</td>
  </tr>

  <tr>
    <td>Profile</td>
    <td>name=&gt;<i>string</i>, profile=&gt;<i>blob</i>, rendering-intent=&gt;{Undefined, Saturation, Perceptual, Absolute, Relative}, black-point-compensation=&gt;{True, False}</td>
    <td>add or remove ICC or IPTC image profile; name is formal name (e.g. ICC or filename; set profile to <code>''</code> to remove profile</td>
  </tr>

  <tr>
    <td>Quantize</td>
    <td>colors=&gt;<i>integer</i>, colorspace=&gt;{RGB, Gray, Transparent, OHTA, XYZ, YCbCr, YIQ, YPbPr, YUV, CMYK, sRGB, HSL, HSB}, treedepth=&gt; <i>integer</i>, dither=&gt;{True, False}, dither-method=&gt;{Riemersma, Floyd-Steinberg}, measure_error=&gt;{True, False}, global_colormap=&gt;{True, False}, transparent-color=&gt;<i>color</i></td>
    <td>preferred number of colors in the image</td>
  </tr>

  <tr>
    <td>Raise</td>
    <td>geometry=&gt;<i>geometry</i>, width=&gt;<i>integer</i>, height=&gt;<i>integer</i>, x=&gt;<i>integer</i>, y=&gt;<i>integer</i>, raise=&gt;{True, False}</td>
    <td>lighten or darken image edges to create a 3-D effect</td>
  </tr>

  <tr>
    <td>ReduceNoise</td>
    <td>geometry=&gt;<i>geometry</i>, width=&gt;<i>integer</i>, height=&gt;<i>integer</i>, channel=&gt;{All, Default, Alpha, Black, Blue, CMYK, Cyan, Gray, Green, Index, Magenta, Opacity, Red, RGB, Yellow}</td>
    <td>reduce noise in the image with a noise peak elimination filter</td>
  </tr>

  <tr>
    <td>Remap</td>
    <td>image=&gt;<i>image-handle</i>,  dither=&gt;{true, false}, dither-method=&gt;{Riemersma, Floyd-Steinberg}</td>
    <td>replace the colors of an image with the closest color from a reference image.</td>
  </tr>

  <tr>
    <td>Resample</td>
    <td>density=&gt;<i>geometry</i>, x=&gt;<i>double</i>, y=&gt;<i>double</i>, filter=&gt;{Point, Box, Triangle, Hermite, Hanning, Hamming, Blackman, Gaussian, Quadratic, Cubic, Catrom, Mitchell, Lanczos, Bessel, Sinc}, support=&gt;<i>double</i></td>
    <td>resample image to desired resolution. Specify <code>blur</code> &gt; 1 for blurry or &lt; 1 for sharp</td>
  </tr>

  <tr>
    <td>Resize</td>
    <td>geometry=&gt;<i>geometry</i>, width=&gt;<i>integer</i>, height=&gt;<i>integer</i>, filter=&gt;{Point, Box, Triangle, Hermite, Hanning, Hamming, Blackman, Gaussian, Quadratic, Cubic, Catrom, Mitchell, Lanczos, Bessel, Sinc}, support=&gt;<i>double</i>, blur=&gt;<i>double</i></td>
    <td>scale image to desired size. Specify <code>blur</code> &gt; 1 for blurry or &lt; 1 for sharp</td>
  </tr>

  <tr>
    <td>Roll</td>
    <td>geometry=&gt;<i>geometry</i>, x=&gt;<i>integer</i>, y=&gt;<i>integer</i></td>
    <td>roll an image vertically or horizontally</td>
  </tr>

  <tr>
    <td>Rotate</td>
    <td>degrees=&gt;<i>double</i>, background=&gt;<i><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></i></td>
    <td>rotate an image</td>
  </tr>

  <tr>
    <td>RotationalBlur</td>
    <td>geometry=&gt;<i>geometry</i>, angle=&gt;<i>double</i>, bias=&gt;<i>double</i>, channel=&gt;{All, Default, Alpha, Black, Blue, CMYK, Cyan, Gray, Green, Index, Magenta, Opacity, Red, RGB, Yellow}</td>
    <td>radial blur the image.</td>
  </tr>

  <tr>
    <td>Sample</td>
    <td>geometry=&gt;<i>geometry</i>, width=&gt;<i>integer</i>, height=&gt;<i>integer</i></td>
    <td>scale image with pixel sampling.</td>
  </tr>

  <tr>
    <td>Scale</td>
    <td>geometry=&gt;<i>geometry</i>, width=&gt;<i>integer</i>, height=&gt;<i>integer</i></td>
    <td>scale image to desired size</td>
  </tr>

  <tr>
    <td>Segment</td>
    <td>colorspace=&gt;{RGB, Gray, Transparent, OHTA, XYZ, YCbCr, YCC, YIQ, YPbPr, YUV, CMYK}, verbose={True, False}, cluster-threshold=&gt;<i>double</i>, smoothing-threshold=<i>double</i></td>
    <td>segment an image by analyzing the histograms of the color components and identifying units that are homogeneous</td>
  </tr>

  <tr>
    <td>SelectiveBlur</td>
    <td>geometry=&gt;<i>geometry</i>, radius=&gt;<i>double</i>, sigma=&gt;<i>double</i>, threshold=&gt;<i>double</i>, bias=&gt;<i>double</i>, channel=&gt;{All, Default, Alpha, Black, Blue, CMYK, Cyan, Gray, Green, Index, Magenta, Opacity, Red, RGB, Yellow}</td>
    <td>selectively blur pixels within a contrast threshold.</td>
  </tr>
  <tr>
    <td>Separate</td>
    <td>channel=&gt;{Red, RGB, All, etc.}</td>
    <td>separate a channel from the image into a grayscale image</td>
  </tr>

  <tr>
    <td>Shade</td>
    <td>geometry=&gt;<i>geometry</i>, azimuth=&gt;<i>double</i>, elevation=&gt;<i>double</i>, gray=&gt;{true, false}</td>
    <td>shade the image using a distant light source</td>
  </tr>

  <tr>
    <td>SetPixel</td>
    <td>geometry=&gt;<i>geometry</i>, channel=&gt;{All, Default, Alpha, Black, Blue, CMYK, Cyan, Gray, Green, Index, Magenta, Opacity, Red, RGB, Yellow}, color=&gt;<i>array of float values</i>, x=&gt;<i>integer</i>, y=&gt;<i>integer</i>, color=&gt;<i>array of float values</i></td>
    <td>set a single pixel.  By default normalized pixel values are expected.</td>
  </tr>

  <tr>
    <td>Shadow</td>
    <td>geometry=&gt;<i>geometry</i>, opacity=&gt;<i>double</i>, sigma=&gt;<i>double</i>, x=&gt;<i>integer</i>, y=&gt;<i>integer</i></td>
    <td>simulate an image shadow</td>
  </tr>

  <tr>
    <td>Sharpen</td>
    <td>geometry=&gt;<i>geometry</i>, radius=&gt;<i>double</i>, sigma=&gt;<i>double</i>, bias=&gt;<i>double</i>, channel=&gt;{All, Default, Alpha, Black, Blue, CMYK, Cyan, Gray, Green, Index, Magenta, Opacity, Red, RGB, Yellow}</td>
    <td>sharpen the image with a Gaussian operator of the given radius and standard deviation (sigma).</td>
  </tr>

  <tr>
    <td>Shave</td>
    <td>geometry=&gt;<i>geometry</i>, width=&gt;<i>integer</i>, height=&gt;<i>integer</i></td>
    <td>shave pixels from the image edges</td>
  </tr>

  <tr>
    <td>Shear</td>
    <td>geometry=&gt;<i>geometry</i>, x=&gt;<i>double</i>, y=&gt;<i>double</i> fill=&gt;<i><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></i></td>
    <td>shear the image along the X or Y axis by a positive or negative shear angle</td>
  </tr>

  <tr>
    <td>SigmoidalContrast</td>
    <td>geometry=&gt;<i>string</i>, 'contrast'=&gt;<i>double</i>, 'mid-point'=&gt;<i>double</i> channel=&gt;{Red, RGB, All, etc.}, sharpen=&gt;{True, False}</td>
    <td>sigmoidal non-lineraity contrast control.  Increase the contrast of the image using a sigmoidal transfer function without saturating highlights or shadows. <var>Contrast</var> indicates how much to increase the contrast (0 is none; 3 is typical; 20 is a lot);  <var>mid-point</var> indicates where midtones fall in the resultant image (0 is white; 50% is middle-gray; 100% is black). To decrease contrast, set sharpen to False.</td>
  </tr>

  <tr>
    <td>Signature</td>
    <td><br /></td>
    <td>generate an SHA-256 message digest for the image pixel stream</td>
  </tr>

  <tr>
    <td>Sketch</td>
    <td>geometry=&gt;<i>geometry</i>, radius=&gt;<i>double</i>, sigma=&gt;<i>double</i>, angle=&gt;<i>double</i></td>
    <td>sketch the image with a Gaussian operator of the given radius and standard deviation (sigma) at the given angle</td>
  </tr>

  <tr>
    <td>Solarize</td>
    <td>geometry=&gt;<i>string</i>, threshold=&gt;<i>double</i>, channel=&gt;{All, Default, Alpha, Black, Blue, CMYK, Cyan, Gray, Green, Index, Magenta, Opacity, Red, RGB, Yellow}</td>
    <td>negate all pixels above the threshold level</td>
  </tr>

  <tr>
    <td>SparseColor</td>
    <td>points=&gt;<i>array of float values</i>, method=&gt;{Barycentric, Bilinear, Shepards, Voronoi}, 'virtual-pixel'=&gt;{Background Black Constant Dither Edge Gray Mirror Random Tile Transparent White}</td>
    <td>interpolate the image colors around the supplied points</td>
  </tr>

  <tr>
    <td>Splice</td>
    <td>geometry=&gt;<i>geometry</i>, width=&gt;<i>integer</i>, height=&gt;<i>integer</i>, x=&gt;<i>integer</i>, y=&gt;<i>integer</i>, fuzz=&gt;<i>double</i>, background=&gt;<i><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></i>, gravity=&gt;{NorthWest, North, NorthEast, West, Center, East, SouthWest, South, SouthEast}</td>
    <td>splice an image</td>
  </tr>

  <tr>
    <td>Spread</td>
    <td>radius=&gt;<i>double</i>, interpolate=&gt;{undefined, average, bicubic, bilinear, mesh, nearest-neighbor, spline}</td>
    <td>displace image pixels by a random amount</td>
  </tr>

  <tr>
    <td>Statistic</td>
    <td>geometry=&gt;<i>geometry</i>, width=&gt;<i>integer</i>, height=&gt;<i>integer</i>, channel=&gt;{All, Default, Alpha, Black, Blue, CMYK, Cyan, Gray, Green, Index, Magenta, Opacity, Red, RGB, Yellow}, type=&gt;{Median, Mode, Mean, Maximum, Minimum, ReduceNoise, RMS}</td>
    <td>replace each pixel with corresponding statistic from the neighborhood.</td>
  </tr>
  <tr>
    <td>Stegano</td>
    <td>image=&gt;<i>image-handle</i>, offset=&gt;<i>integer</i></td>
    <td>hide a digital watermark within the image</td>
  </tr>

  <tr>
    <td>Stereo</td>
    <td>image=&gt;<i>image-handle</i>, x=&gt;<i>integer</i>, y=&gt;<i>integer</i></td>
    <td>composites two images and produces a single image that is the composite of a left and right image of a stereo pair</td>
  </tr>

  <tr>
    <td>Strip</td>
    <td><br /></td>
    <td>strip an image of all profiles and comments.</td>
  </tr>

  <tr>
    <td>Swirl</td>
    <td>degrees=&gt;<i>double</i>, interpolate=&gt;{undefined, average, bicubic, bilinear, mesh, nearest-neighbor, spline}</td>
    <td>swirl image pixels about the center</td>
  </tr>

  <tr>
    <td>Texture</td>
    <td>texture=&gt;<i>image-handle</i></td>
    <td>name of texture to tile onto the image background</td>
  </tr>

  <tr>
    <td>Thumbnail</td>
    <td>geometry=&gt;<i>geometry</i>, width=&gt;<i>integer</i>, height=&gt;<i>integer</i></td>
    <td>changes the size of an image to the given dimensions and removes any associated profiles.</td>
  </tr>

  <tr>
    <td>Threshold</td>
    <td>threshold=&gt;<i>string</i>, channel=&gt;{All, Default, Alpha, Black, Blue, CMYK, Cyan, Gray, Green, Index, Magenta, Opacity, Red, RGB, Yellow}</td>
    <td>threshold the image</td>
  </tr>

  <tr>
    <td>Tint</td>
    <td>fill=&gt;<i><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></i>, blend=&gt;<i>string</i></td>
    <td>tint the image with the fill color.</td>
  </tr>

  <tr>
    <td>Transparent</td>
    <td>color=&gt;<i><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></i>, invert=&gt;{True, False}</td>
    <td>make this color transparent within the image</td>
  </tr>

  <tr>
    <td>Transpose</td>
    <td><br /></td>
    <td>flip image in the vertical direction and rotate 90 degrees</td>
  </tr>

  <tr>
    <td>Transverse</td>
    <td><br /></td>
    <td>flop image in the horizontal direction and rotate 270 degrees</td>
  </tr>

  <tr>
    <td>Trim</td>
    <td><br /></td>
    <td>remove edges that are the background color from the image</td>
  </tr>

  <tr>
    <td>UnsharpMask</td>
    <td>geometry=&gt;<i>geometry</i>, radius=&gt;<i>double</i>, sigma=&gt;<i>double</i>, gain=&gt;<i>double</i>, threshold=&gt;<i>double</i></td>
    <td>sharpen the image with the unsharp mask algorithm.</td>
  </tr>

  <tr>
    <td>Vignette</td>
    <td>geometry=&gt;<i>geometry</i>, radius=&gt;<i>double</i>, sigma=&gt;<i>double</i>, x=&gt;<i>integer</i>, y=&gt;<i>integer</i>, background=&gt;<i><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></i></td>
    <td>offset the edges of the image in vignette style</td>
  </tr>

  <tr>
    <td>Wave</td>
    <td>geometry=&gt;<i>geometry</i>, amplitude=&gt;<i>double</i>, wavelength=&gt;<i>double</i>, interpolate=&gt;{undefined, average, bicubic, bilinear, mesh, nearest-neighbor, spline}</td>
    <td>alter an image along a sine wave</td>
  </tr>

  <tr>
    <td>WaveDenoise</td>
    <td>geometry=&gt;<i>geometry</i>, threshold=&gt;<i>double</i>, threshold=&gt;<i>double</i></td>
    <td>removes noise from the image using a wavelet transform</td>
  </tr>

  <tr>
    <td>WhiteThreshold</td>
    <td>threshold=&gt;<i>string</i>, , channel=&gt;{All, Default, Alpha, Black, Blue, CMYK, Cyan, Gray, Green, Index, Magenta, Opacity, Red, RGB, Yellow}</td>
    <td>force all pixels above the threshold intensity into white</td>
  </tr>
</tbody>
</table>

<p>Note, that the <code>geometry</code> parameter is a short cut for the <code>width</code> and <code>height</code> parameters (e.g.  <code>geometry=&gt;'106x80'</code> is equivalent to <code>width=&gt;106, height=&gt;80</code> ).</p>

<p>You can specify <code>@filename</code> in both Annotate() and Draw(). This reads the text or graphic primitive instructions from a file on disk. For example,</p>

<pre><code>
image-&gt;Draw(fill=&gt;'red', primitive=&gt;'rectangle',
 points=&gt;'20,20 100,100  40,40 200,200  60,60 300,300');
</code></pre>

<p>Is equivalent to</p>

<pre><code>
$image-&gt;Draw(fill=&gt;'red', primitive=&gt;'@draw.txt');
</code></pre>

<p>Where <code>draw.txt</code> is a file on disk that contains this:</p>

<pre><code>
rectangle 20, 20 100, 100
rectangle 40, 40 200, 200
rectangle 60, 60 300, 300
</code></pre>

<p>The <i>text</i> parameter for methods, Annotate(), Comment(), Draw(), and Label() can include the image filename, type, width, height, or other image attribute by embedding these special format characters:</p>

<pre class="pre-scrollable"><code>%b   file size
%c   comment
%d   directory
%e   filename extension
%f   filename
%g   page geometry
%h   height
%i   input filename
%k   number of unique colors
%l   label
%m   magick
%n   number of scenes
%o   output filename
%p   page number
%q   quantum depth
%r   image class and colorspace
%s   scene number
%t   top of filename
%u   unique temporary filename
%w   width
%x   x resolution
%y   y resolution
%z   image depth
%C   image compression type
%D   image dispose method
%H   page height
%Q   image compression quality
%T   image delay
%W   page width
%X   page x offset
%Y   page y offset
%@   bounding box
%#   signature
%%   a percent sign
\n   newline
\r   carriage return
</code></pre>

<p>For example,</p>

<pre><code>
text=&gt;"%m:%f %wx%h"
</code></pre>

<p>produces an annotation of <b>MIFF:bird.miff 512x480</b> for an image titled <b>bird.miff</b> and whose width is 512 and height is 480.</p>

<p>You can optionally add <i>Image</i> to any method name. For example, TrimImage() is an alias for method Trim().</p>

<p>Most of the attributes listed above have an analog in <a href="<?php echo $_SESSION['RelativePath']?>/../script/convert.php">convert</a>. See the documentation for a more detailed description of these attributes.</p>

<h2 class="magick-header"><a id="set-attribute"></a>Set an Image Attribute</h2>

<p>Use method Set() to set an image attribute. For example,</p>

<pre><code>
$image-&gt;Set(dither=&gt;'True');
$image-&gt;[$x]-&gt;Set(delay=&gt;3);
</code></pre>

<p>Where this example uses 'True' and this document says '{True, False}',
you can use the case-insensitive strings 'True' and 'False', or you
can use the integers 1 and 0.</p>

<p>When you call Get() on a Boolean attribute, Image::Magick returns 1 or 0, not a string.</p>

<p>And here is a list of all the image attributes you can set:</p>

<table class="table table-sm table-striped">
  <caption>Image Attributes</caption>
  <tbody>
  <tr>
    <th>Attribute</th>
    <th style="width: 40%">Values</th>
    <th style="width: 40%">Description</th>
  </tr>

  <tr>
    <td>adjoin</td>
    <td>{True, False}</td>
    <td>join images into a single multi-image file</td>
  </tr>

  <tr>
    <td>alpha</td>
    <td>{On, Off, Opaque, Transparent, Copy, Extract, Set}</td>
    <td>control of and special operations involving the alpha/matte channel</td>
  </tr>

  <tr>
    <td>antialias</td>
    <td>{True, False}</td>
    <td>remove pixel aliasing</td>
  </tr>

  <tr>
    <td>area-limit</td>
    <td><i>integer</i></td>
    <td>set pixel area resource limit.</td>
  </tr>

  <tr>
    <td>attenuate</td>
    <td><i>double</i></td>
    <td>lessen (or intensify) when adding noise to an image.</td>
  </tr>

  <tr>
    <td>authenticate</td>
    <td><i>string</i></td>
    <td>decrypt image with this password.</td>
  </tr>

  <tr>
    <td>background</td>
    <td><i><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></i></td>
    <td>image background color</td>
  </tr>

  <tr>
    <td>blue-primary</td>
    <td><i>x-value</i>, <i>y-value</i></td>
    <td>chromaticity blue primary point (e.g. 0.15, 0.06)</td>
  </tr>

  <tr>
    <td>bordercolor</td>
    <td><i><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></i></td>
    <td>set the image border color</td>
  </tr>

  <tr>
    <td>clip-mask</td>
    <td><i>image</i></td>
    <td>associate a clip mask with the image.</td>
  </tr>

  <tr>
    <td>colormap[<i>i</i>]</td>
    <td><i><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></i></td>
    <td>color name (e.g. red) or hex value (e.g. #ccc) at position
<i>i</i></td>
  </tr>

  <tr>
    <td>comment</td>
    <td><i>string</i></td>
    <td>set the image comment</td>
  </tr>

  <tr>
    <td>compression</td>
    <td>{None, BZip, Fax, Group4, JPEG, JPEG2000, LosslessJPEG, LZW, RLE, Zip}</td>
    <td>type of image compression</td>
  </tr>

  <tr>
    <td>debug</td>
    <td>{All, Annotate, Blob, Cache, Coder, Configure, Deprecate, Draw, Exception, Locale, None, Resource, Transform, X11}</td>
    <td>display copious debugging information</td>
  </tr>

  <tr>
    <td>delay</td>
    <td><i>integer</i></td>
    <td>this many 1/100ths of a second must expire before displaying the next image in a sequence</td>
  </tr>

  <tr>
    <td>density</td>
    <td><i>geometry</i></td>
    <td>vertical and horizontal resolution in pixels of the image</td>
  </tr>

  <tr>
    <td>depth</td>
    <td><i>integer</i></td>
    <td>image depth</td>
  </tr>

  <tr>
    <td>direction</td>
    <td><i>{Undefined, right-to-left, left-to-right</i></td>
    <td>render text right-to-left or left-to-right</td>
  </tr>

  <tr>
    <td>disk-limit</td>
    <td><i>integer</i></td>
    <td>set disk resource limit</td>
  </tr>

  <tr>
    <td>dispose</td>
    <td><i>{Undefined, None, Background, Previous}</i></td>
    <td>layer disposal method</td>
  </tr>

  <tr>
    <td>dither</td>
    <td>{True, False}</td>
    <td>apply error diffusion to the image</td>
  </tr>

  <tr>
    <td>display</td>
    <td><i>string</i></td>
    <td>specifies the X server to contact</td>
  </tr>

  <tr>
    <td>extract</td>
    <td><i>geometry</i></td>
    <td>extract area from image</td>
  </tr>

  <tr>
    <td>file</td>
    <td><i>filehandle</i></td>
    <td>set the image filehandle</td>
  </tr>

  <tr>
    <td>filename</td>
    <td><i>string</i></td>
    <td>set the image filename</td>
  </tr>

  <tr>
    <td>fill</td>
    <td><i>color</i></td>
    <td>The fill color paints any areas inside the outline of drawn shape.</td>
  </tr>

  <tr>
    <td>font</td>
    <td><i>string</i></td>
    <td>use this font when annotating the image with text</td>
  </tr>

  <tr>
    <td>fuzz</td>
    <td><i>integer</i></td>
    <td>colors within this distance are considered equal</td>
  </tr>

  <tr>
    <td>gamma</td>
    <td><i>double</i></td>
    <td>gamma level of the image</td>
  </tr>

  <tr>
    <td>Gravity</td>
    <td>{Forget, NorthWest, North, NorthEast, West, Center, East, SouthWest, South, SouthEast}</td>
    <td>type of image gravity</td>
  </tr>

  <tr>
    <td>green-primary</td>
    <td><i>x-value</i>, <i>y-value</i></td>
    <td>chromaticity green primary point (e.g. 0.3, 0.6)</td>
  </tr>

  <tr>
    <td>index[<i>x</i>, <i>y</i>]</td>
    <td><i>string</i></td>
    <td>colormap index at position (<i>x</i>, <i>y</i>)</td>
  </tr>

  <tr>
    <td>interlace</td>
    <td>{None, Line, Plane, Partition, JPEG, GIF, PNG}</td>
    <td>the type of interlacing scheme</td>
  </tr>

  <tr>
    <td>iterations</td>
    <td><i>integer</i></td>
    <td>add Netscape loop extension to your GIF animation</td>
  </tr>

  <tr>
    <td>label</td>
    <td><i>string</i></td>
    <td>set the image label</td>
  </tr>

  <tr>
    <td>loop</td>
    <td><i>integer</i></td>
    <td>add Netscape loop extension to your GIF animation</td>
  </tr>

  <tr>
    <td>magick</td>
    <td><i>string</i></td>
    <td>set the image format</td>
  </tr>

  <tr>
    <td>map-limit</td>
    <td><i>integer</i></td>
    <td>set map resource limit</td>
  </tr>

  <tr>
    <td>mask</td>
    <td><i>image</i></td>
    <td>associate a mask with the image.</td>
  </tr>

  <tr>
    <td>matte</td>
    <td>{True, False}</td>
    <td>enable the image matte channel</td>
  </tr>

  <tr>
    <td>mattecolor</td>
    <td><i><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></i></td>
    <td>set the image matte color</td>
  </tr>

  <tr>
    <td>memory-limit</td>
    <td><i>integer</i></td>
    <td>set memory resource limit</td>
  </tr>

  <tr>
    <td>monochrome</td>
    <td>{True, False}</td>
    <td>transform the image to black and white</td>
  </tr>

  <tr>
    <td>option</td>
    <td><i>string</i></td>
    <td>associate an option with an image format (e.g.  option=&gt;'ps:imagemask'</td>
  </tr>

  <tr>
    <td>orientation</td>
    <td>{top-left, top-right, bottom-right, bottom-left, left-top, right-top, right-bottom, left-bottom}</td>
    <td>image orientation</td>
  </tr>

  <tr>
    <td>page</td>
    <td>{ Letter, Tabloid, Ledger, Legal, Statement, Executive, A3, A4, A5, B4, B5, Folio, Quarto, 10x14} or <i>geometry</i></td>
    <td>preferred size and location of an image canvas</td>
  </tr>

  <tr>
    <td>pixel[<i>x</i>, <i>y</i>]</td>
    <td><i>string</i></td>
    <td>hex value (e.g. #ccc) at position (<i>x</i>, <i>y</i>)</td>
  </tr>

  <tr>
    <td>pointsize</td>
    <td><i>integer</i></td>
    <td>pointsize of the Postscript or TrueType font</td>
  </tr>

  <tr>
    <td>quality</td>
    <td><i>integer</i></td>
    <td>JPEG/MIFF/PNG compression level</td>
  </tr>

  <tr>
    <td>red-primary</td>
    <td><i>x-value</i>, <i>y-value</i></td>
    <td>chromaticity red primary point (e.g. 0.64, 0.33)</td>
  </tr>

  <tr>
    <td>sampling-factor</td>
    <td><i>geometry</i></td>
    <td>horizontal and vertical sampling factor</td>
  </tr>

  <tr>
    <td>scene</td>
    <td><i>integer</i></td>
    <td>image scene number</td>
  </tr>

  <tr>
    <td>server</td>
    <td><i>string</i></td>
    <td>specifies the X server to contact</td>
  </tr>

  <tr>
    <td>size</td>
    <td><i>string</i></td>
    <td>width and height of a raw image</td>
  </tr>

  <tr>
    <td>stroke</td>
    <td><i>color</i></td>
    <td>The stroke color paints along the outline of a shape.</td>
  </tr>

  <tr>
    <td>texture</td>
    <td><i>string</i></td>
    <td>name of texture to tile onto the image background</td>
  </tr>

  <tr>
    <td>tile-offset</td>
    <td><i>geometry</i></td>
    <td>image tile offset</td>
  </tr>

  <tr>
    <td>time-limit</td>
    <td><i>integer</i></td>
    <td>set time resource limit in seconds</td>
  </tr>

  <tr>
    <td>type</td>
    <td>{Bilevel, Grayscale, GrayscaleMatte, Palette, PaletteMatte, TrueColor, TrueColorMatte, ColorSeparation, ColorSeparationMatte}</td>
    <td>image type</td>
  </tr>

  <tr>
    <td>units</td>
    <td>{ Undefined, PixelsPerInch, PixelsPerCentimeter}</td>
    <td>units of image resolution</td>
  </tr>

  <tr>
    <td>verbose</td>
    <td>{True, False}</td>
    <td>print detailed information about the image</td>
  </tr>

  <tr>
    <td>virtual-pixel</td>
    <td>{Background Black Constant Dither Edge Gray Mirror Random Tile Transparent White}</td>
    <td>the virtual pixel method</td>
  </tr>

  <tr>
    <td>white-point</td>
    <td><i>x-value</i>, <i>y-value</i></td>
    <td>chromaticity white point (e.g. 0.3127, 0.329)</td>
  </tr>
  </tbody>
</table>

<p>Note, that the <code>geometry</code> parameter is a short cut for the <code>width</code> and <code>height</code> parameters (e.g.  <code>geometry=&gt;'106x80'</code> is equivalent to <code>width=&gt;106, height=&gt;80</code>).</p>

<p>SetAttribute() is an alias for method Set().</p>

<p>Most of the attributes listed above have an analog in
<a href="<?php echo $_SESSION['RelativePath']?>/../script/convert.php">convert</a>. See the documentation for a more detailed description of these attributes.</p>

<h2 class="magick-header"><a id="get-attribute"></a>Get an Image Attribute</h2>

<p>Use method Get() to get an image attribute. For example,</p>

<pre><code>
($a, $b, $c) = $image-&gt;Get('colorspace', 'magick', 'adjoin');
$width = $image-&gt;[3]-&gt;Get('columns');
</code></pre>

<p>In addition to all the attributes listed in <a href="#set-attribute">Set an Image Attribute</a> , you can get these additional attributes:</p>

<table class="table table-sm table-striped">
  <caption>Image Attributes</caption>
  <tbody>
  <tr>
    <th>Attribute</th>
    <th>Values</th>
    <th style="width: 60%">Description</th>
  </tr>

  <tr>
    <td>area</td>
    <td><i>integer</i></td>
    <td>current area resource consumed</td>
  </tr>

  <tr>
    <td>base-columns</td>
    <td><i>integer</i></td>
    <td>base image width (before transformations)</td>
  </tr>

  <tr>
    <td>base-filename</td>
    <td><i>string</i></td>
    <td>base image filename (before transformations)</td>
  </tr>

  <tr>
    <td>base-rows</td>
    <td><i>integer</i></td>
    <td>base image height (before transformations)</td>
  </tr>

  <tr>
    <td>class</td>
    <td>{Direct, Pseudo}</td>
    <td>image class</td>
  </tr>

  <tr>
    <td>colors</td>
    <td><i>integer</i></td>
    <td>number of unique colors in the image</td>
  </tr>

  <tr>
    <td>columns</td>
    <td><i>integer</i></td>
    <td>image width</td>
  </tr>

  <tr>
    <td>copyright</td>
    <td><i>string</i></td>
    <td>get PerlMagick's copyright</td>
  </tr>

  <tr>
    <td>directory</td>
    <td><i>string</i></td>
    <td>tile names from within an image montage</td>
  </tr>

  <tr>
    <td>elapsed-time</td>
    <td><i>double</i></td>
    <td>elapsed time in seconds since the image was created</td>
  </tr>

  <tr>
    <td>error</td>
    <td><i>double</i></td>
    <td>the mean error per pixel computed with methods Compare() or Quantize()</td>
  </tr>

  <tr>
    <td>bounding-box</td>
    <td><i>string</i></td>
    <td>image bounding box</td>
  </tr>

  <tr>
    <td>disk</td>
    <td><i>integer</i></td>
    <td>current disk resource consumed</td>
  </tr>

  <tr>
    <td>filesize</td>
    <td><i>integer</i></td>
    <td>number of bytes of the image on disk</td>
  </tr>

  <tr>
    <td>format</td>
    <td><i>string</i></td>
    <td>get the descriptive image format</td>
  </tr>

  <tr>
    <td>geometry</td>
    <td><i>string</i></td>
    <td>image geometry</td>
  </tr>

  <tr>
    <td>height</td>
    <td><i>integer</i></td>
    <td>the number of rows or height of an image</td>
  </tr>

  <tr>
    <td>icc</td>
    <td><i>string</i></td>
    <td>ICC profile</td>
  </tr>

  <tr>
    <td>icc</td>
    <td><i>string</i></td>
    <td>ICM profile</td>
  </tr>

  <tr>
    <td>id</td>
    <td><i>integer</i></td>
    <td>ImageMagick registry id</td>
  </tr>

  <tr>
    <td>IPTC</td>
    <td><i>string</i></td>
    <td>IPTC profile</td>
  </tr>

  <tr>
    <td>mean-error</td>
    <td><i>double</i></td>
    <td>the normalized mean error per pixel computed with methods Compare() or Quantize()</td>
  </tr>

  <tr>
    <td>map</td>
    <td><i>integer</i></td>
    <td>current memory-mapped resource consumed</td>
  </tr>

  <tr>
    <td>matte</td>
    <td>{True, False}</td>
    <td>whether or not the image has a matte channel</td>
  </tr>

  <tr>
    <td>maximum-error</td>
    <td><i>double</i></td>
    <td>the normalized max error per pixel computed with methods Compare() or Quantize()</td>
  </tr>

  <tr>
    <td>memory</td>
    <td><i>integer</i></td>
    <td>current memory resource consumed</td>
  </tr>

  <tr>
    <td>mime</td>
    <td><i>string</i></td>
    <td>MIME of the image format</td>
  </tr>

  <tr>
    <td>montage</td>
    <td><i>geometry</i></td>
    <td>tile size and offset within an image montage</td>
  </tr>

  <tr>
    <td>page.x</td>
    <td><i>integer</i></td>
    <td>x offset of image virtual canvas</td>
  </tr>

  <tr>
    <td>page.y</td>
    <td><i>integer</i></td>
    <td>y offset of image virtual canvas</td>
  </tr>

  <tr>
    <td>rows</td>
    <td><i>integer</i></td>
    <td>the number of rows or height of an image</td>
  </tr>

  <tr>
    <td>signature</td>
    <td><i>string</i></td>
    <td>SHA-256 message digest associated with the image pixel stream</td>
  </tr>

  <tr>
    <td>taint</td>
    <td>{True, False}</td>
    <td>True if the image has been modified</td>
  </tr>

  <tr>
    <td>total-ink-density</td>
    <td><i>double</i></td>
    <td>returns the total ink density for a CMYK image</td>
  </tr>

  <tr>
    <td>transparent-color</td>
    <td><i><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></i></td>
    <td>set the image transparent color</td>
  </tr>

  <tr>
    <td>user-time</td>
    <td><i>double</i></td>
    <td>user time in seconds since the image was created</td>
  </tr>

  <tr>
    <td>version</td>
    <td><i>string</i></td>
    <td>get PerlMagick's version</td>
  </tr>

  <tr>
    <td>width</td>
    <td><i>integer</i></td>
    <td>the number of columns or width of an image</td>
  </tr>

  <tr>
    <td>XMP</td>
    <td><i>string</i></td>
    <td>XMP profile</td>
  </tr>

  <tr>
    <td>x-resolution</td>
    <td><i>integer</i></td>
    <td>x resolution of the image</td>
  </tr>

  <tr>
    <td>y-resolution</td>
    <td><i>integer</i></td>
    <td>y resolution of the image</td>
  </tr>
  </tbody>
</table>

<p>GetAttribute() is an alias for method Get().</p>

<p>Most of the attributes listed above have an analog in
<a href="<?php echo $_SESSION['RelativePath']?>/../script/convert.php">convert</a>. See the documentation for a more detailed description of these attributes.</p>

<h2 class="magick-header"><a id="compare"></a>Compare an Image to its Reconstruction</h2>

<p>Mathematically and visually annotate the difference between an image and its reconstruction with the Compare() method.  The method supports these parameters:</p>

<table class="table table-sm table-striped">
  <caption>Compare Parameters</caption>
  <tbody>
  <tr>
    <th>Parameter</th>
    <th style="width: 40%">Values</th>
    <th style="width: 40%">Description</th>
  </tr>

  <tr>
    <td>channel</td>
    <td><i>double</i></td>
    <td>select image channels, the default is all channels except alpha.</td>
  </tr>

  <tr>
    <td>fuzz</td>
    <td><i>double</i></td>
    <td>colors within this distance are considered equal</td>
  </tr>

  <tr>
    <td>image</td>
    <td><i>image-reference</i></td>
    <td>the image reconstruction</td>
  </tr>

  <tr>
    <td>metric</td>
    <td>AE, MAE, MEPP, MSE, PAE, PSNR, RMSE</td>
    <td>measure differences between images with this metric</td>
  </tr>
  </tbody>
</table>

<p>In this example, we compare the ImageMagick logo to a sharpened reconstruction:</p>

<pre><code>
use Image::Magick;

$logo=Image::Magick->New();
$logo->Read('logo:');
$sharp=Image::Magick->New();
$sharp->Read('logo:');
$sharp->Sharpen('0x1');
$difference=$logo->Compare(image=>$sharp, metric=>'rmse');
print $difference->Get('error'), "\n";
$difference->Display();
</code></pre>

<p>In addition to the reported root mean squared error of around 0.024, a difference image is displayed so you can visually identify the difference between the images.</p>

<h2 class="magick-header"><a id="montage"></a>Create an Image Montage</h2>

<p>Use method Montage() to create a composite image by combining several separate images. The images are tiled on the composite image with the name of the image optionally appearing just below the individual tile. For example,</p>

<pre><code>
$image-&gt;Montage(geometry=&gt;'160x160', tile=&gt;'2x2', texture=&gt;'granite:');
</code></pre>

<p>And here is a list of Montage() parameters you can set:</p>

<table class="table table-sm table-striped">
  <caption>Montage Parameters</caption>
  <tbody>
  <tr>
    <th>Parameter</th>
    <th style="width: 40%">Values</th>
    <th style="width: 40%">Description</th>
  </tr>

  <tr>
    <td>background</td>
    <td><i><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></i></td>
    <td>background color name</td>
  </tr>

  <tr>
    <td>border</td>
    <td><i>integer</i></td>
    <td>image border width</td>
  </tr>

  <tr>
    <td>filename</td>
    <td><i>string</i></td>
    <td>name of montage image</td>
  </tr>

  <tr>
    <td>fill</td>
    <td><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></td>
    <td>fill color for annotations</td>
  </tr>

  <tr>
    <td>font</td>
    <td><i>string</i></td>
    <td>X11 font name</td>
  </tr>

  <tr>
    <td>frame</td>
    <td><i>geometry</i></td>
    <td>surround the image with an ornamental border</td>
  </tr>

  <tr>
    <td>geometry</td>
    <td><i>geometry</i></td>
    <td>preferred tile and border size of each tile of the composite
image (e.g. 120x120+4+3>)</td>
  </tr>

  <tr>
    <td>gravity</td>
    <td>NorthWest, North, NorthEast, West, Center, East, SouthWest,
South, SouthEast</td>
    <td>direction image gravitates to within a tile</td>
  </tr>

  <tr>
    <td>label</td>
    <td><i>string</i></td>
    <td>assign a label to an image</td>
  </tr>

  <tr>
    <td>mode</td>
    <td>Frame, Unframe, Concatenate</td>
    <td>thumbnail framing options</td>
  </tr>

  <tr>
    <td>pointsize</td>
    <td><i>integer</i></td>
    <td>pointsize of the Postscript or TrueType font</td>
  </tr>

  <tr>
    <td>shadow</td>
    <td>{True, False}</td>
    <td>add a shadow beneath a tile to simulate depth</td>
  </tr>

  <tr>
    <td>stroke</td>
    <td><a href="<?php echo $_SESSION['RelativePath']?>/../script/color.php">color name</a></td>
    <td>stroke color for annotations</td>
  </tr>

  <tr>
    <td>texture</td>
    <td><i>string</i></td>
    <td>name of texture to tile onto the image background</td>
  </tr>

  <tr>
    <td>tile</td>
    <td><i>geometry</i></td>
    <td>the number of tiles per row and page (e.g. 6x4)</td>
  </tr>

  <tr>
    <td>title</td>
    <td>string</td>
    <td>assign a title to the image montage</td>
  </tr>

  <tr>
    <td>transparent</td>
    <td><i>string</i></td>
    <td>make this color transparent within the image</td>
  </tr>
  </tbody>
</table>

<p>Note, that the <code>geometry</code> parameter is a short cut for the <code>width</code> and <code>height</code> parameters (e.g.  <code>geometry=&gt;'106x80'</code> is equivalent to <code>width=&gt;106, height=&gt;80</code>).</p>

<p>MontageImage() is an alias for method Montage().</p>

<p>Most of the attributes listed above have an analog in <a href="<?php echo $_SESSION['RelativePath']?>/../script/montage.php">montage</a>. See the documentation for a more detailed description of these attributes.</p>

<h2 class="magick-header"><a id="blobs"></a>Working with Blobs</h2>

<p>A blob contains data that directly represent a particular image
format in memory instead of on disk. PerlMagick supports
blobs in any of these image <a href="<?php echo $_SESSION['RelativePath']?>/../script/formats.php">formats</a> and provides methods to convert a blob to or from a particular image format.</p>

<table class="table table-sm table-striped">
  <caption>Blob Methods</caption>
  <tbody>
  <tr>
    <th>Method</th>
    <th>Parameters</th>
    <th>Return Value</th>
    <th>Description</th>
  </tr>

  <tr>
    <td>ImageToBlob</td>
    <td>any image <a href="#set-attribute">attribute</a></td>
    <td>an array of image data in the respective image format</td>
    <td>convert an image or image sequence to an array of blobs</td>
  </tr>

  <tr>
    <td>BlobToImage</td>
    <td>one or more blobs</td>
    <td>the number of blobs converted to an image</td>
    <td>convert one or more blobs to an image</td>
  </tr>
  </tbody>
</table>

<p>ImageToBlob() returns the image data in their respective formats. You can then print it, save it to an ODBC database, write it to a file, or pipe it to a display program:</p>

<pre><code>
@blobs = $image-&gt;ImageToBlob();
open(DISPLAY,"| display -") || die;
binmode DISPLAY;
print DISPLAY $blobs[0];
close DISPLAY;
</code></pre>

<p>Method BlobToImage() returns an image or image sequence converted from the supplied blob:</p>

<pre><code>
@blob=$db-&gt;GetImage();
$image=Image::Magick-&gt;new(magick=&gt;'jpg');
$image-&gt;BlobToImage(@blob);
</code></pre>

<h2 class="magick-header"><a id="direct-access"></a>Direct-access to Image Pixels</h2>

<p>Use these methods to obtain direct access to the image pixels:</p>

<table class="table table-sm table-striped">
<caption>Direct-access to Image Pixels</caption>
<tbody>
  <tr>
    <th>Method</th>
    <th>Parameters</th>
    <th style="width: 50%">Description</th>
  </tr>

  <tr>
    <td>GetAuthenticPixels</td>
    <td>geometry=&gt;<i>geometry</i>, width=&gt;<i>integer</i>, height=&gt;<i>integer</i>, x=&gt;<i>integer</i>, y=&gt;<i>integer</i></td>
    <td>return authentic pixels as a C pointer</td>
  </tr>

  <tr>
    <td>GetVirtualPixels</td>
    <td>geometry=&gt;<i>geometry</i>, width=&gt;<i>integer</i>, height=&gt;<i>integer</i>, x=&gt;<i>integer</i>, y=&gt;<i>integer</i></td>
    <td>return virtual pixels as a const C pointer</td>
  </tr>

  <tr>
    <td>GetAuthenticIndexQueue</td>
    <td></td>
    <td>return colormap indexes or black pixels as a C pointer</td>
  </tr>

  <tr>
    <td>GetVirtualIndexQueue</td>
    <td></td>
    <td>return colormap indexes or black pixels as a const C pointer</td>
  </tr>

  <tr>
    <td>SyncAuthenticPixels</td>
    <td></td>
    <td>sync authentic pixels to pixel cache</td>
  </tr>

</tbody>
</table>

<h2 class="magick-header"><a id="miscellaneous"></a>Miscellaneous Methods</h2>

<p>The Append() method append a set of images. For example,</p>

<pre><code>
$p = $image-&gt;Append(stack=&gt;{true,false});
</code></pre>

<p>appends all the images associated with object <code>$image</code>. By default, images are stacked left-to-right. Set <code>stack</code> to True to stack them top-to-bottom.</p>

<p>The Clone() method copies a set of images. For example,</p>

<pre><code>
$q = $p-&gt;Clone();
</code></pre>

<p>copies all the images from object <code>$p</code> to <code>$q</code>. You can use this method for single or multi-image sequences.</p>

<p>The ComplexImages() method performs complex mathematics on an image sequence. For example,</p>

<pre><code>
$p = $image-&gt;ComplexImages('conjugate');
</code></pre>

<p>The EvaluateImages() method applies an arithmetic, logical or relational expression to a set of images. For example,</p>


<pre><code>
$p = $image-&gt;EvaluateImages('mean');
</code></pre>

<p>averages all the images associated with object <code>$image</code>.</p>

<p>The Features() method returns features for each channel in the image in each of four directions (horizontal, vertical, left and right diagonals) for the specified distance.  The features include the angular second momentum, contrast, correlation, sum of squares: variance, inverse difference moment, sum average, sum varience, sum entropy, entropy, difference variance, difference entropy, information measures of correlation 1, information measures of correlation 2, and maximum correlation coefficient.  Values in RGB, CMYK, RGBA, or CMYKA order (depending on the image type).</p>

<pre><code>
@features = $image-&gt;Features(1);
</code></pre>

<p>Finally, the Transform() method accepts a fully-qualified geometry specification for cropping or resizing one or more images.  For example,</p>

<pre><code>
$p = $images-&gt;Transform(crop=>'100x100+10+60');
</code></pre>

<p>The Flatten() method flattens a set of images and returns it. For example,</p>

<pre><code>
$p = $images-&gt;Flatten(background=&gt;'none');
$p-&gt;Write('flatten.png');
</code></pre>

<p>The sequence of images is replaced by a single image created by composing each image after the first over the first image.</p>

<p>The Fx() method applies a mathematical expression to a set of images and returns the results. For example,</p>

<pre><code>
$p = $image-&gt;Fx(expression=&gt;'(g+b)/2.0',channel=&gt;'red');
$p-&gt;Write('fx.miff');
</code></pre>

<p>replaces the red channel with the average of the green and blue channels.</p>

<p>See <a href="<?php echo $_SESSION['RelativePath']?>/../script/fx.php">FX, The Special Effects Image Operator</a> for a detailed discussion of this method.</p>

<p>Histogram() returns the unique colors in the image and a count for each one. The returned values are an array of red, green, blue, opacity, and count values.</p>

<p>The Morph() method morphs a set of images. Both the image pixels and size are linearly interpolated to give the appearance of a meta-morphosis from one image to the next:</p>

<pre><code>
$p = $image-&gt;Morph(frames=&gt;<i>integer</i>);
</code></pre>

<p>where <i>frames</i> is the number of in-between images to generate.  The default is 1.</p>

<p>Mosaic() creates an mosaic from an image sequence.</p>

<p>Method Mogrify() is a single entry point for the image manipulation methods (<a href="#manipulate">Manipulate an Image</a>). The parameters are the name of a method followed by any parameters the method may require. For example, these calls are equivalent:</p>

<pre><code>
$image-&gt;Crop('340x256+0+0');
$image-&gt;Mogrify('crop', '340x256+0+0');
</code></pre>

<p>Method MogrifyRegion() applies a transform to a region of the image. It is similar to Mogrify() but begins with the region geometry. For example, suppose you want to brighten a 100x100 region of your image at location (40, 50):</p>

<pre><code>
$image-&gt;MogrifyRegion('100x100+40+50', 'modulate', brightness=&gt;50);
</code></pre>

<p>Ping() is a convenience method that returns information about an image without having to read the image into memory. It returns the width, height, file size in bytes, and the file format of the image. You can specify more than one filename but only one filehandle:</p>

<pre><code>
($width, $height, $size, $format) = $image-&gt;Ping('logo.png');
($width, $height, $size, $format) = $image-&gt;Ping(file=&gt;\*IMAGE);
($width, $height, $size, $format) = $image-&gt;Ping(blob=&gt;$blob);
</code></pre>

<p>This a more efficient and less memory intensive way to query if an image exists and what its characteristics are.</p>

<p>Poly() builds a polynomial from the image sequence and the corresponding terms (coefficients and degree pairs):</p>

<pre><code>
$p = $image-&gt;Poly([0.5,1.0,0.25,2.0,1.0,1.0]);
</code></pre>

<p>PreviewImage() tiles 9 thumbnails of the specified image with an image processing operation applied at varying strengths. This may be helpful pin-pointing an appropriate parameter for a particular image processing operation. Choose from these operations: <code>Rotate, Shear, Roll, Hue, Saturation, Brightness, Gamma, Spiff, Dull, Grayscale, Quantize, Despeckle, ReduceNoise, AddNoise, Sharpen, Blur, Threshold, EdgeDetect, Spread, Solarize, Shade, Raise, Segment, Swirl, Implode, Wave, OilPaint, CharcoalDrawing, JPEG</code>. Here is an example:</p>

<pre><code>
$preview = $image-&gt;Preview('Gamma');
$preview-&gt;Display();
</code></pre>

<p>To have full control over text positioning you need font metric information. Use</p>

<pre><code>
($x_ppem, $y_ppem, $ascender, $descender, $width, $height, $max_advance) =
  $image-&gt;QueryFontMetrics(<i>parameters</i>);
</code></pre>

<p>Where <i>parameters</i> is any parameter of the <a href="#manipulate">Annotate</a> method. The return values are:</p>

<ol>
  <li>character width</li>
  <li>character height</li>
  <li>ascender</li>
  <li>descender</li>
  <li>text width</li>
  <li>text height</li>
  <li>maximum horizontal advance</li>
  <li>bounds: x1</li>
  <li>bounds: y1</li>
  <li>bounds: x2</li>
  <li>bounds: y2</li>
  <li>origin: x</li>
  <li>origin: y</li>
</ol>

<p>Use QueryMultilineFontMetrics() to get the maximum text width and height for multiple lines of text.</p>

<p>Call QueryColor() with no parameters to return a list of known colors names or specify one or more color names to get these attributes: red, green, blue, and opacity value.</p>

<pre><code>
@colors = $image-&gt;QueryColor();
($red, $green, $blue, $opacity) = $image-&gt;QueryColor('cyan');
($red, $green, $blue, $opacity) = $image-&gt;QueryColor('#716bae');
</code></pre>

<p>QueryColorname() accepts a color value and returns its respective name or hex value;</p>

<pre><code>
$name = $image-&gt;QueryColorname('rgba(80,60,0,0)');
</code></pre>

<p>Call QueryFont() with no parameters to return a list of known fonts or specify one or more font names to get these attributes: font name, description, family, style, stretch, weight, encoding, foundry, format, metrics, and glyphs values.</p>

<pre><code>
@fonts = $image-&gt;QueryFont();
$weight = ($image-&gt;QueryFont('Helvetica'))[5];
</code></pre>

<p>Call QueryFormat() with no parameters to return a list of known image formats or specify one or more format names to get these attributes: adjoin, blob support, raw, decoder, encoder, description, and module.</p>

<pre><code>
@formats = $image-&gt;QueryFormat();
($adjoin, $blob_support, $raw, $decoder, $encoder, $description, $module) =
  $image-&gt;QueryFormat('gif');
</code></pre>

<p>Call MagickToMime() with the image format name to get its MIME type such as <code>image/tiff</code> from <code>tif</code>.</p>

<pre><code>
$mime = $image-&gt;MagickToMime('tif');
</code></pre>

<p>Use RemoteCommand() to send a command to an already running <a href="<?php echo $_SESSION['RelativePath']?>/../script/display.php">display</a> or <a href="<?php echo $_SESSION['RelativePath']?>/../script/animate.php">animate</a> application. The only parameter is the name of the image file to display or animate.</p>

<pre><code>
$image-&gt;RemoteCommand('image.jpg');
</code></pre>

<p>The Smush() method smushes a set of images together. For example,</p>

<pre><code>
$p = $image-&gt;Smush(stack=&gt;{true,false},offset=&gt;<var>integer</var>);
</code></pre>

<p>smushes together all the images associated with object <code>$image</code>. By default, images are smushed left-to-right. Set <code>stack</code> to True to smushed them top-to-bottom.</p>

<p>Statistics() returns the image statistics for each channel in the image. The returned values are an array of depth, minima, maxima, mean, standard deviation, kurtosis, skewness, and entropy values in RGB, CMYK, RGBA, or CMYKA order (depending on the image type).</p>

<pre><code>
@statistics = $image-&gt;Statistics();
</code></pre>

<p>Finally, the Transform() method accepts a fully-qualified geometry specification for cropping or resizing one or more images.  For example,</p>

<pre><code>
$p = $image-&gt;Transform(crop=&gt;'100x100+0+0');
</code></pre>

<p>You can optionally add <i>Image</i> to any method name above. For example, PingImage() is an alias for method Ping().</p>

<h2 class="magick-header"><a id="exceptions"></a>Handling Exceptions</h2>

<p>All PerlMagick methods return an undefined string context upon success. If any problems occur, the error is returned as a string with an embedded numeric status code. A status code less than 400 is a warning. This means that the operation did not complete but was recoverable to some degree. A numeric code greater or equal to 400 is an error and indicates the operation failed completely. Here is how exceptions are returned for the different methods:</p>

<p>Methods which return a number (e.g. Read(), Write()):</p>

<pre><code>
$x = $image-&gt;Read(...);
warn "$x" if "$x";      # print the error message
$x =~ /(\d+)/;
print $1;               # print the error number
print 0+$x;             # print the number of images read
</code></pre>

<p>Methods which operate on an image (e.g. Resize(), Crop()):</p>

<pre><code>
$x = $image-&gt;Crop(...);
warn "$x" if "$x";      # print the error message
$x =~ /(\d+)/;
print $1;               # print the error number
</code></pre>

<p>Methods which return images (EvaluateSequence(), Montage(), Clone()) should be checked for errors this way:</p>

<pre><code>
$x = $image-&gt;Montage(...);
warn "$x" if !ref($x);  # print the error message
$x =~ /(\d+)/;
print $1;               # print the error number
</code></pre>

<p>Here is an example error message:</p>

<pre><code>
Error 400: Memory allocation failed
</code></pre>

<p>Review the complete list of <a href="<?php echo $_SESSION['RelativePath']?>/../script/exception.php">error and warning codes</a>.</p>

<p>The following illustrates how you can use a numeric status code:</p>

<pre><code>
$x = $image-&gt;Read('rose.png');
$x =~ /(\d+)/;
die "unable to continue" if ($1 == ResourceLimitError);
</code></pre>

<h2 class="magick-header"><a id="constants"></a>Constants</h2>

<p>PerlMagick includes these constants:</p>

<pre class="pre-scrollable"><code>BlobError
BlobWarning
CacheError
CacheWarning
CoderError
CoderWarning
ConfigureError
ConfigureWarning
CorruptImageError
CorruptImageWarning
DelegateError
DelegateWarning
DrawError
DrawWarning
ErrorException
FatalErrorException
FileOpenError
FileOpenWarning
ImageError
ImageWarning
MissingDelegateError
MissingDelegateWarning
ModuleError
ModuleWarning
Opaque
OptionError
OptionWarning
QuantumDepth
QuantumRange
RegistryError
RegistryWarning
ResourceLimitError
ResourceLimitWarning
StreamError
StreamWarning
Success
Transparent
TypeError
TypeWarning
WarningException
XServerError
XServerWarning
</code></pre>

<p>You can access them like this:</p>

<pre><code>
Image::Magick-&gt;QuantumDepth
</code></pre>

</div>
