/* This class is part of the XP framework
 *
 * $Id$
 */

package net.xp_framework.cmd;

import java.lang.annotation.Retention;
import java.lang.annotation.Target;
import java.lang.annotation.ElementType;
import java.lang.annotation.RetentionPolicy;

@Retention(RetentionPolicy.RUNTIME)
@Target(ElementType.PARAMETER)
public @interface Default {
    String value() default "";
}
